                  <!-- ==================== FOOTER ==================== -->
                  <div class="flf-footer">
                     <p>&copy; <?php echo date('Y'); ?> <a href="https://fairlawfirmltd.com/" target="_blank" rel="noopener">Fair Law Firm LTD</a><span class="flf-sep">&middot;</span>All rights reserved.</p>
                  </div>

               </div>
               <!-- end .padding_infor_info / page content -->
            </div>
            <!-- end #content -->
         </div>
         <!-- end .inner_container -->
      </div>
      <!-- end .full_container -->


      <!-- ==================== SCRIPTS ==================== -->

      <!-- jQuery (required by Chart.js, perfect-scrollbar, legacy code) -->
      <script src="js/jquery.min.js"></script>

      <!-- Bootstrap 5 bundle (includes Popper) -->
      <script src="js/bootstrap5.bundle.min.js"></script>

      <!-- Sidebar scrollbar + custom -->
      <script src="js/perfect-scrollbar.min.js"></script>
      <script src="js/custom.js"></script>

      <!-- Chart.js (used by dashboard analytics via its own inline script) -->
      <script src="js/Chart.min.js"></script>

      <!-- Mobile sidebar: close on outside click or Escape key -->
      <script>
      (function () {
         'use strict';

         var mq      = window.matchMedia('(max-width: 1199px)');
         var sidebar = document.getElementById('sidebar');

         if (!sidebar) return;

         function closeMobileMenu() {
            if (mq.matches && sidebar.classList.contains('active')) {
               sidebar.classList.remove('active');
            }
         }

         document.addEventListener('click', function (e) {
            if (!mq.matches || !sidebar.classList.contains('active')) return;
            if (e.target.closest('#sidebar') || e.target.closest('#sidebarCollapse')) return;
            closeMobileMenu();
         });

         document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeMobileMenu();
         });
      })();
      </script>

   </body>
</html>
