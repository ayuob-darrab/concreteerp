{{-- Data Table Component --}}
@props([
    'columns' => [],
    'data' => [],
    'id' => 'dataTable',
    'responsive' => true,
    'striped' => true,
    'hover' => true,
    'emptyMessage' => 'لا توجد بيانات',
    'emptyIcon' => 'inbox',
])

<div class="card">
    {{-- Card Header (optional slot) --}}
    @isset($header)
        <div class="card-header d-flex justify-content-between align-items-center">
            {{ $header }}
        </div>
    @endisset

    <div class="card-body p-0">
        <div class="{{ $responsive ? 'table-responsive' : '' }}">
            <table id="{{ $id }}"
                class="table {{ $striped ? 'table-striped' : '' }} {{ $hover ? 'table-hover' : '' }} mb-0">
                <thead class="table-dark">
                    <tr>
                        @foreach ($columns as $key => $column)
                            <th @if (is_array($column)) @if (isset($column['width'])) style="width: {{ $column['width'] }}" @endif
                                @if (isset($column['class'])) class="{{ $column['class'] }}" @endif @endif
                                >
                                {{ is_array($column) ? $column['label'] : $column }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            {{ $row }}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) }}" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-{{ $emptyIcon }} fa-3x mb-3"></i>
                                    <p class="mb-0">{{ $emptyMessage }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Card Footer (optional slot) --}}
    @isset($footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endisset
</div>
