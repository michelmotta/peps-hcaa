import Swal from 'sweetalert2';
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";

export function setupGlobalLibraries() {
    // Fancybox setup
    Fancybox.bind('[data-fancybox]');
    
    // Global confirm delete function
    window.confirmDelete = function (formId) {
        Swal.fire({
            title: 'Você quer mesmo apagar?',
            text: "Esta ação não poderá ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    };
}