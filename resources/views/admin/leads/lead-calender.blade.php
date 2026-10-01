 <div class="row">

     <div class="col-lg-12">
         <div class="card card-outline card-primary " id="calendarCard">
             <div class="card-header">
                 <h3 class="card-title">Lead Follow-ups</h3>
                 <div class="card-tools">
                     <button type="button" class="btn btn-tool" data-card-widget="collapse">
                         <i class="fas fa-minus"></i>
                     </button>
                 </div>
             </div>
             <div class="card-body">
                 <div class="cal-legend">
                     <span class="lg-pending">Has follow-up</span>
                     <span class="lg-overdue">Overdue (not followed up)</span>
                     {{-- <span class="lg-done">Followed up</span> --}}
                     <span class="lg-today">Today</span>
                 </div>
                 <div id="leadCalendar"></div>
             </div>
         </div>
     </div>
 </div>
