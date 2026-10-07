// Entry area admin: Gentelella v4 (vanilla JS, tanpa Bootstrap/jQuery).
//
// Sidebar, topbar, dan footer dirender oleh Blade (layouts/admin.blade.php),
// sehingga mountShell() hanya memasang perilakunya: buka/tutup sidebar,
// submenu, dan pengalih tema terang/gelap. Entry demo Gentelella
// (command palette, data contoh, form palsu) sengaja tidak dimuat.
import 'gentelella/scss/v4/main.scss';
import { mountShell } from 'gentelella/v4/shell';

mountShell();

// Form dengan atribut data-konfirmasi (mis. tombol hapus) meminta
// konfirmasi sebelum dikirim.
document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.dataset.konfirmasi) {
        return;
    }

    if (!window.confirm(form.dataset.konfirmasi)) {
        event.preventDefault();
    }
});
