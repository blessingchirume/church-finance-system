// import './bootstrap';
//
// import Alpine from 'alpinejs';
//
// import $ from 'jquery';
// import 'select2';
// import 'select2/dist/css/select2.css';
// import '@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css';
//
//
// window.$ = window.jQuery = $;
// window.Alpine = Alpine;
// // alert(1)
// Alpine.start();
//
// // Initialize Select2
// // $(document).ready(function() {
// //     $('.select2').select2({
// //         theme: 'bootstrap4',
// //         width: '100%',
// //         dropdownAutoWidth: true,
// //         dropdownParent: $('body') // Fix for Bootstrap 5 modal issue
// //     });
// // });
//
// // document.addEventListener('alpine:init', () => {
// //     $('.select2').select2({
// //         theme: 'bootstrap4',
// //         width: '100%'
// //     });
// // });
//
// // document.addEventListener('DOMContentLoaded', function() {
// //     // Vanilla JS alternative to jQuery's $(document).ready()
// //     const selectElements = document.querySelectorAll('.select2');
// //
// //     selectElements.forEach(select => {
// //         $(select).select2({
// //             theme: 'bootstrap4',
// //             width: '100%',
// //             dropdownAutoWidth: true,
// //             dropdownParent: document.body // Fix for Bootstrap 5 modal issue
// //         });
// //     });
// // });


// Import jQuery first
// import $ from 'jquery';

// Set jQuery globally before importing Select2
// window.$ = window.jQuery = $;

// Now import Select2
// import 'select2';
//
// // Import styles
// import 'select2/dist/css/select2.css';
// import '@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css';

// Verification function
// function verifyDependencies() {
//     if (typeof $ === 'undefined') {
//         console.error('jQuery not loaded!');
//         return false;
//     }
//     if (typeof $.fn.select2 === 'undefined') {
//         console.error('Select2 plugin not registered!');
//         return false;
//     }
//     return true;
// }

// Initialize Select2
// function initializeSelect2() {
//     if (!verifyDependencies()) return;
//
//     $('.select2').select2({
//         theme: 'bootstrap4',
//         width: '100%',
//         dropdownAutoWidth: true,
//         dropdownParent: document.body
//     });
// }

// Initialize when ready
// if (document.readyState === 'complete') {
//     initializeSelect2();
// } else {
//     document.addEventListener('DOMContentLoaded', initializeSelect2);
// }

// For dynamic content
// document.addEventListener('livewire:load', initializeSelect2);
