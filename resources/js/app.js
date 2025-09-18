import './bootstrap';

import jQuery from 'jquery';
window.$ = jQuery;
window.jQuery = jQuery; // certaines libs en ont besoin

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import Toastr from 'toastr';
window.toastr = Toastr;

// ✅ Typeahead (plugin jQuery, pas d’export ESM)
import 'bootstrap-3-typeahead';

// Extensions que tu utilises
import 'datatables.net-buttons-bs5';
import 'datatables.net-select-bs5';

// (Optionnel) si tu veux les modules HTML5/print, garde-les comme tu as :
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';


