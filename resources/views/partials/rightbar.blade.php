{{-- Right-hand settings panel shared by every page. --}}
        <!-- Right Sidebar -->
        <div class="right-bar">
            <div data-simplebar class="h-100">
                <div class="rightbar-title d-flex align-items-center px-3 py-4">
            
                    <h5 class="m-0 me-2">Settings</h5>

                    <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                        <i class="mdi mdi-close noti-icon"></i>
                    </a>
                </div>

                <!-- Settings -->
                <hr class="mt-0" />
                <h6 class="text-center mb-0">Appearance</h6>

                {{-- same choices as the switch in the top bar (theme.js keeps them in sync) --}}
                <div class="p-4 d-grid gap-3">
                    <button type="button" class="btn btn-outline-secondary text-start p-2 theme-panel-option" data-theme-set="light">
                        <img src="/assets/images/layouts/layout-1.jpg" class="img-thumbnail mb-2" alt="Light theme preview">
                        <span class="d-block"><i class="bx bx-sun me-1"></i> Light</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary text-start p-2 theme-panel-option" data-theme-set="dark">
                        <img src="/assets/images/layouts/layout-2.jpg" class="img-thumbnail mb-2" alt="Dark theme preview">
                        <span class="d-block"><i class="bx bx-moon me-1"></i> Dark</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary text-start p-2 theme-panel-option" data-theme-set="system">
                        <span class="d-block"><i class="bx bx-desktop me-1"></i> Same as device</span>
                        <small class="text-muted">Follows your computer or phone's light/dark setting</small>
                    </button>
                </div>
                <style>.theme-panel-option[aria-pressed="true"] { border-color: var(--bs-primary); box-shadow: 0 0 0 2px var(--bs-primary); }</style>
            </div> <!-- end slimscroll-menu-->
        </div>
        <!-- /Right-bar -->
