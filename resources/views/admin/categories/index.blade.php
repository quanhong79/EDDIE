@extends('layouts.app')

@section('title','Categories')

@section('content')
<div class="container">
  <div class="row align-items-center mb-3">
    <div class="col">
      <h2 class="mb-0">Categories</h2>
    </div>
    <div class="col text-right">
      <a class="btn btn-success" href="{{ route('admin.categories.create') }}">
        + Create New Category
      </a>
    </div>
  </div>

  {{-- Flash messages --}}
  @if ($message = session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $message }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
      </ul>
    </div>
  @endif

  {{-- Filters --}}
  <form method="GET" action="{{ route('admin.categories.index') }}" class="form-inline mb-3" id="cat-filter-form">
    {{-- Search name/slug --}}
    <input
      type="text"
      name="q"
      class="form-control mr-2 mb-2"
      placeholder="Search name/slug..."
      value="{{ request('q') }}"
      style="min-width:260px;"
    >

    {{-- Parent filter --}}
    <select name="parent_id" class="form-control mr-2 mb-2" id="parentSelect" style="min-width:220px;">
      @php $pid = request('parent_id'); @endphp
      <option value="">— All parents —</option>
      <option value="0" @selected($pid==='0')>Only root categories</option>
      @foreach(($parents ?? collect()) as $p)
        <option value="{{ $p->id }}" @selected((string)$pid === (string)$p->id)>{{ $p->name }}</option>
      @endforeach
    </select>

    <button class="btn btn-outline-secondary mb-2">Filter</button>
    @if(request()->hasAny(['q','parent_id']))
      <a href="{{ route('admin.categories.index') }}" class="btn btn-link mb-2">Clear</a>
    @endif
  </form>

  <div class="table-responsive">
    <table class="table table-bordered align-middle mb-0">
      <thead class="thead-light">
        <tr>
          <th style="width:80px;">ID</th>
          <th style="min-width:220px;">Name</th>
          <th style="min-width:220px;">Slug</th>
          <th style="min-width:200px;">Parent</th>
          <th style="width:200px;">Children</th>
          <th style="width:240px;">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($categories as $category)
          <tr>
            <td>{{ $category->id }}</td>
            <td class="font-weight-semibold">{{ $category->name }}</td>
            <td class="text-monospace">{{ $category->slug }}</td>
            <td>{{ optional($category->parent)->name ?? '—' }}</td>
            <td>
              @if(isset($category->children_count))
                <span class="badge badge-info">{{ $category->children_count }}</span>
              @else
                {{-- nếu controller chưa withCount --}}
                <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              <div class="d-flex flex-wrap" style="gap:.5rem;">
                @if(app('router')->has('admin.categories.show'))
                  <a class="btn btn-info btn-sm" href="{{ route('admin.categories.show', $category) }}">Show</a>
                @endif

                <a class="btn btn-primary btn-sm" href="{{ route('admin.categories.edit', $category) }}">Edit</a>

                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                      onsubmit="return confirm('Delete this category?')" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted">No categories.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  @if(method_exists($categories, 'links'))
    <div class="d-flex justify-content-center mt-3">
      {{ $categories->withQueryString()->links() }}
    </div>
  @endif
</div>

@push('scripts')
<script>
  // Auto submit khi đổi parent
  document.addEventListener('DOMContentLoaded', function () {
    var sel = document.getElementById('parentSelect');
    var form = document.getElementById('cat-filter-form');
    if (sel && form) {
      sel.addEventListener('change', function(){ form.submit(); });
    }
  });
</script>
@endpush
@endsection
