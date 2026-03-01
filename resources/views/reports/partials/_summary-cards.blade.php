{{-- ملخص التقرير --}}
<div class="row mb-4">
    @foreach ($summaryCards as $card)
        <div class="col-md-{{ $card['col'] ?? 3 }}">
            <div class="card border-0 shadow-sm {{ $card['bg'] ?? 'bg-white' }}">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">{{ $card['label'] }}</h6>
                            <h3 class="mb-0 {{ $card['textClass'] ?? '' }}">
                                @if (isset($card['format']) && $card['format'] === 'currency')
                                    {{ number_format($card['value'], 2) }}
                                    <small class="fs-6">د.ع</small>
                                @elseif(isset($card['format']) && $card['format'] === 'number')
                                    {{ number_format($card['value']) }}
                                @else
                                    {{ $card['value'] }}
                                @endif
                            </h3>
                        </div>
                        <div class="summary-icon {{ $card['iconBg'] ?? 'bg-primary' }} bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-{{ $card['icon'] }} fa-lg {{ $card['iconColor'] ?? 'text-primary' }}"></i>
                        </div>
                    </div>
                    @if (isset($card['trend']))
                        <div class="mt-2">
                            <small class="{{ $card['trend']['direction'] === 'up' ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-{{ $card['trend']['direction'] }}"></i>
                                {{ $card['trend']['value'] }}%
                            </small>
                            <small class="text-muted">مقارنة بالفترة السابقة</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
