import './stimulus_bootstrap.js';

// Import styles
import './styles/admin.scss';

// Import jQuery (required by AdminLTE and DataTables)
import $ from 'jquery';
window.$ = window.jQuery = $;

// Import Bootstrap (bundled with AdminLTE)
import 'bootstrap';

// Import AdminLTE
import 'admin-lte';

// Import DataTables
import 'datatables.net-bs5';

// Import admin.js for modal and other admin functionality
import './admin.js';

console.log('AdminLTE application loaded successfully!');
