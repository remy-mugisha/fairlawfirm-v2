/*------------------------------------------------------------------
    File Name: custom.js
    Template Name: Pluto - Responsive HTML5 Template
    Trimmed for Fair Law Firm LTD admin CMS.
    Keeps only functional behaviour used by the admin shell:
      - sidebar collapse/expand toggle
      - perfect-scrollbar on the sidebar
    Removed legacy Pluto demo wiring (calendar, data-toggle tooltip,
    demo Chart.js canvases) that threw errors on real pages.
-------------------------------------------------------------------*/

"use strict";

$(document).ready(function () {
  /*-- sidebar toggle --*/
  $('#sidebarCollapse').on('click', function () {
    $('#sidebar').toggleClass('active');
  });
});

/*-- scrollbar js --*/
if (typeof PerfectScrollbar !== 'undefined' && document.getElementById('sidebar')) {
  new PerfectScrollbar('#sidebar');
}
