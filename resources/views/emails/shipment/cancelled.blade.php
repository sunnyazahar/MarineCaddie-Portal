@php
    $normalized = preg_replace("/\r\n|\r|\n/", "\n", $body ?? '');
    $lines = explode("\n", $normalized);
    $cancellationLine = 'The shipment has been cancelled.';
@endphp
@foreach ($lines as $line)
    @if ($line === $cancellationLine)
        <strong style="color:#dc2626;font-weight:700;">{{ $cancellationLine }}</strong>
    @else
        {!! e($line) !!}
    @endif
    @if (! $loop->last)
        <br>
    @endif
@endforeach
