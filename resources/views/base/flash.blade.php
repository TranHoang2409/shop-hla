@php
    $flashMessages = collect([
        'success' => session('success'),
        'warning' => session('warning'),
        'error' => session('error'),
    ])->filter();

    $flashMeta = [
        'success' => ['title' => 'Thành công', 'icon' => 'fa-check'],
        'warning' => ['title' => 'Cần chú ý', 'icon' => 'fa-exclamation-triangle'],
        'error' => ['title' => 'Không thành công', 'icon' => 'fa-times'],
        'danger' => ['title' => 'Có lỗi xảy ra', 'icon' => 'fa-times'],
    ];
@endphp

@if ($flashMessages->isNotEmpty() || $errors->any())
    <div class="store-flash-stack" aria-live="polite" aria-atomic="true">
        @foreach ($flashMessages as $type => $message)
            <div class="store-flash store-flash-{{ $type }}" data-store-flash role="status">
                <span class="store-flash-icon">
                    <i class="fas {{ $flashMeta[$type]['icon'] }}"></i>
                </span>
                <span class="store-flash-content">
                    <strong>{{ $flashMeta[$type]['title'] }}</strong>
                    <span>{{ $message }}</span>
                </span>
                <button type="button" class="store-flash-close" data-store-flash-close aria-label="Đóng thông báo">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endforeach

        @if ($errors->any())
            <div class="store-flash store-flash-error" data-store-flash role="alert">
                <span class="store-flash-icon">
                    <i class="fas {{ $flashMeta['danger']['icon'] }}"></i>
                </span>
                <span class="store-flash-content">
                    <strong>{{ $flashMeta['danger']['title'] }}</strong>
                    <span>{{ $errors->first() }}</span>
                </span>
                <button type="button" class="store-flash-close" data-store-flash-close aria-label="Đóng thông báo">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
    </div>
@endif
