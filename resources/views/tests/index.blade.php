@foreach ($lines as $line)
@php
$line = trim(preg_replace('/\s+/', ' ', $line));
@endphp
<ul>
  <li>{{ $line }}</li>
</ul>
@endforeach
