{{-- Variabel: $action, $statusOptions, $categories, $rooms --}}
<form method="GET" action="{{ $action }}" class="card-s mb-3">
    <div class="card-body-s">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Tiket / judul">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($statusOptions as $val => $label)
                        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected((string) request('category_id') === (string) $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Lokasi</label>
                <select name="room_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($rooms as $r)
                        <option value="{{ $r->id }}" @selected((string) request('room_id') === (string) $r->id)>{{ $r->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label small mb-1">Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control px-2">
            </div>
            <div class="col-6 col-md-1">
                <label class="form-label small mb-1">Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control px-2">
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button class="btn btn-primary flex-fill" title="Terapkan"><i class="bi bi-funnel"></i></button>
                <a href="{{ $action }}" class="btn btn-light" title="Reset"><i class="bi bi-x-lg"></i></a>
            </div>
        </div>
    </div>
</form>
