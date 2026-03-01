<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupportTicketController extends Controller
{
    /**
     * عرض قائمة تذاكر الشركة
     */
    public function index(Request $request)
    {
        $companyCode = Auth::user()->company_code;

        $query = SupportTicket::where('company_code', $companyCode)
            ->withCount(['replies' => function ($q) {
                $q->where('is_internal', false);
            }])
            ->orderBy('created_at', 'desc');

        // فلترة حسب الحالة
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->has('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }

        // بحث
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $tickets = $query->paginate(15);

        // إحصائيات
        $stats = [
            'total' => SupportTicket::where('company_code', $companyCode)->count(),
            'open' => SupportTicket::where('company_code', $companyCode)->where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('company_code', $companyCode)->where('status', 'in_progress')->count(),
            'pending' => SupportTicket::where('company_code', $companyCode)->where('status', 'pending_response')->count(),
            'resolved' => SupportTicket::where('company_code', $companyCode)->where('status', 'resolved')->count(),
        ];

        return view('company.support.index', compact('tickets', 'stats'));
    }

    /**
     * عرض نموذج إنشاء تذكرة جديدة
     */
    public function create()
    {
        $categories = SupportTicket::$categories;
        $priorities = SupportTicket::$priorities;

        return view('company.support.create', compact('categories', 'priorities'));
    }

    /**
     * حفظ تذكرة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,billing,feature_request,bug,general',
            'priority' => 'required|in:low,medium,high,urgent',
            'attachments.*' => 'nullable|file|max:5120' // 5MB max
        ], [
            'subject.required' => 'عنوان التذكرة مطلوب',
            'description.required' => 'وصف المشكلة مطلوب',
            'category.required' => 'يرجى اختيار التصنيف',
            'priority.required' => 'يرجى اختيار الأولوية',
        ]);

        $attachments = [];
        $ticketNumber = SupportTicket::generateTicketNumber();

        if ($request->hasFile('attachments')) {
            $uploadPath = 'uploads/tickets/' . date('Y-m');

            if (!file_exists(public_path($uploadPath))) {
                mkdir(public_path($uploadPath), 0755, true);
            }

            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $fileName);
                $attachments[] = [
                    'name' => $originalName,
                    'path' => $uploadPath . '/' . $fileName,
                    'size' => $fileSize
                ];
            }
        }

        $ticket = SupportTicket::create([
            'ticket_number' => $ticketNumber,
            'company_code' => Auth::user()->company_code,
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'attachments' => $attachments
        ]);

        return redirect()->route('support.index')
            ->with('success', 'تم إرسال التذكرة بنجاح. رقم التذكرة: ' . $ticket->ticket_number);
    }

    /**
     * عرض تفاصيل تذكرة
     */
    public function show($id)
    {
        $companyCode = Auth::user()->company_code;

        $ticket = SupportTicket::where('company_code', $companyCode)
            ->with(['replies' => function ($q) {
                $q->where('is_internal', false)->orderBy('created_at', 'asc');
            }])
            ->findOrFail($id);

        return view('company.support.show', compact('ticket'));
    }

    /**
     * إضافة رد على التذكرة
     */
    public function reply(Request $request, $id)
    {
        $companyCode = Auth::user()->company_code;

        $ticket = SupportTicket::where('company_code', $companyCode)
            ->findOrFail($id);

        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:5120'
        ], [
            'message.required' => 'الرسالة مطلوبة'
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            $uploadPath = 'uploads/tickets/' . date('Y-m');

            if (!file_exists(public_path($uploadPath))) {
                mkdir(public_path($uploadPath), 0755, true);
            }

            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $fileName);
                $attachments[] = [
                    'name' => $originalName,
                    'path' => $uploadPath . '/' . $fileName,
                    'size' => $fileSize
                ];
            }
        }

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_type' => 'customer',
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->fullname,
            'message' => $request->message,
            'attachments' => $attachments
        ]);

        // تحديث حالة التذكرة إذا كانت بانتظار الرد
        if ($ticket->status === 'pending_response') {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'تم إرسال الرد بنجاح');
    }

    /**
     * إغلاق التذكرة
     */
    public function close(Request $request, $id)
    {
        $companyCode = Auth::user()->company_code;

        $ticket = SupportTicket::where('company_code', $companyCode)
            ->findOrFail($id);

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
            'rating' => $request->rating,
            'feedback' => $request->feedback
        ]);

        return back()->with('success', 'تم إغلاق التذكرة. شكراً لتقييمك!');
    }

    /**
     * إعادة فتح التذكرة
     */
    public function reopen($id)
    {
        $companyCode = Auth::user()->company_code;

        $ticket = SupportTicket::where('company_code', $companyCode)
            ->whereIn('status', ['resolved', 'closed'])
            ->findOrFail($id);

        $ticket->update([
            'status' => 'open',
            'resolved_at' => null,
            'closed_at' => null
        ]);

        return back()->with('success', 'تم إعادة فتح التذكرة');
    }
}
