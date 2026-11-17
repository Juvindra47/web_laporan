document.addEventListener('DOMContentLoaded', function () {
  // Navbar landing berubah saat scroll
  var nav = document.querySelector('.site-nav');
  if (nav) {
    var onScroll = function () { nav.classList.toggle('scrolled', window.scrollY > 40); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  // Sidebar mobile
  var sidebar = document.getElementById('sidebar');
  var backdrop = document.getElementById('sidebarBackdrop');
  document.querySelectorAll('[data-toggle-sidebar]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      sidebar.classList.toggle('open');
      backdrop.classList.toggle('show');
    });
  });
  if (backdrop) backdrop.addEventListener('click', function () {
    sidebar.classList.remove('open'); backdrop.classList.remove('show');
  });

  // Tampil/sembunyikan password
  document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.querySelector(btn.getAttribute('data-toggle-password'));
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  });

  // Preview gambar / video sebelum upload
  document.querySelectorAll('input[data-preview]').forEach(function (input) {
    input.addEventListener('change', function () {
      var target = document.querySelector(input.getAttribute('data-preview'));
      var file = input.files && input.files[0];
      if (!target) return;
      if (!file) { target.style.display = 'none'; return; }
      target.src = URL.createObjectURL(file);
      target.style.display = 'block';
      if (target.tagName === 'VIDEO') target.load();
    });
  });

  // Filter laporan pada beranda
  var reportFilter = document.querySelector('[data-report-filter]');
  if (reportFilter) {
    var reportItems = Array.prototype.slice.call(document.querySelectorAll('[data-report-item]'));
    var reportEmpty = document.querySelector('[data-report-empty]');
    var reportCount = document.querySelector('[data-report-count]');

    reportFilter.addEventListener('click', function (e) {
      var button = e.target.closest('[data-filter]');
      if (!button) return;

      var filter = button.getAttribute('data-filter');
      var shown = 0;

      reportFilter.querySelectorAll('[data-filter]').forEach(function (btn) {
        var active = btn === button;
        btn.classList.toggle('is-active', active);
        btn.setAttribute('aria-pressed', active ? 'true' : 'false');
      });

      reportItems.forEach(function (item) {
        var match = filter === 'semua' || item.getAttribute('data-status') === filter;
        item.classList.toggle('d-none', !match);
        if (match) shown++;
      });

      if (reportEmpty) reportEmpty.classList.toggle('d-none', shown > 0);
      if (reportCount) reportCount.textContent = shown;
    });
  }

  // Konfirmasi hapus / aksi penting lewat modal Bootstrap
  var modalEl = document.getElementById('confirmModal');
  if (modalEl && window.bootstrap) {
    var modal = new bootstrap.Modal(modalEl);
    var pendingForm = null;
    document.addEventListener('submit', function (e) {
      var form = e.target;
      var msg = form.getAttribute && form.getAttribute('data-confirm');
      if (!msg || form.dataset.confirmed === '1') return;
      e.preventDefault();
      pendingForm = form;
      document.getElementById('confirmText').textContent = msg;
      modal.show();
    });
    document.getElementById('confirmYes').addEventListener('click', function () {
      if (pendingForm) { pendingForm.dataset.confirmed = '1'; pendingForm.submit(); }
    });
  }
});
