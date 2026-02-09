import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', function() {
    
    // Cari semua form yang punya class 'form-delete'
    const deleteForms = document.querySelectorAll('.form-delete');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // STOP! Jangan submit dulu.

            const dataName = form.getAttribute('data-name') || 'Data ini';

            Swal.fire({
                title: 'Yakin mau hapus?',
                text: `${dataName} akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', // Merah (Bahaya)
                cancelButtonColor: '#3085d6', // Biru (Batal)
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true // Tombol batal di kiri (UX lebih aman)
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kalau user klik YA, baru kita submit form aslinya
                    form.submit();
                }
            });
        });
    });
});