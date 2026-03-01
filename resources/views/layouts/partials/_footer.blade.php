{{-- Footer --}}
<footer class="footer">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span class="text-muted">
                    &copy; {{ date('Y') }}
                    <a href="#" class="text-decoration-none">ConcreteERP</a>
                    - نظام إدارة محطات الخرسانة
                </span>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="text-muted">
                    الإصدار 1.0.0
                    @if (config('app.debug'))
                        | وضع التطوير
                    @endif
                </span>
            </div>
        </div>
    </div>
</footer>
