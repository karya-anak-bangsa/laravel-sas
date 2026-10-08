// Entry area admin: Gentelella v4 (vanilla JS, tanpa Bootstrap/jQuery).
//
// Sidebar, topbar, dan footer dirender oleh Blade (layouts/admin.blade.php),
// sehingga mountShell() hanya memasang perilakunya: buka/tutup sidebar,
// submenu, dan pengalih tema terang/gelap. Entry demo Gentelella
// (command palette, data contoh, form palsu) sengaja tidak dimuat.
import 'gentelella/scss/v4/main.scss';
// Ikon: Font Awesome Free (gaya solid & regular), di-host sendiri lewat Vite.
import '@fortawesome/fontawesome-free/css/fontawesome.min.css';
import '@fortawesome/fontawesome-free/css/solid.min.css';
import '@fortawesome/fontawesome-free/css/regular.min.css';
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

// Elemen dengan data-tampil-untuk="nama_isian:nilai1,nilai2" hanya ditampilkan
// jika isian bernama nama_isian bernilai salah satu dari daftar nilai.
// Tanpa JavaScript semua elemen tetap tampil, dan server mengabaikan isian
// yang tidak relevan.
const elemenBersyarat = document.querySelectorAll('[data-tampil-untuk]');

const perbaruiTampilan = () => {
    elemenBersyarat.forEach((elemen) => {
        const [nama, daftarNilai] = elemen.dataset.tampilUntuk.split(':');
        const isian = elemen.closest('form')?.elements.namedItem(nama);

        if (isian) {
            elemen.hidden = !daftarNilai.split(',').includes(isian.value);
        }
    });
};

if (elemenBersyarat.length > 0) {
    document.addEventListener('change', perbaruiTampilan);
    perbaruiTampilan();
}
