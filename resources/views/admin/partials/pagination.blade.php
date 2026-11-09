@if ($paginator->hasPages())
  <nav class="pager" aria-label="Halaman">
    @if ($paginator->onFirstPage())
      <span class="pg disabled">&lsaquo;</span>
    @else
      <a class="pg" href="{{ $paginator->previousPageUrl() }}">&lsaquo;</a>
    @endif

    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="pg disabled">{{ $element }}</span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="pg active">{{ $page }}</span>
          @else
            <a class="pg" href="{{ $url }}">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    @if ($paginator->hasMorePages())
      <a class="pg" href="{{ $paginator->nextPageUrl() }}">&rsaquo;</a>
    @else
      <span class="pg disabled">&rsaquo;</span>
    @endif
  </nav>
@endif
