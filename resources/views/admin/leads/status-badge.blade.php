 @switch((int) $lead->status)
     @case(1)
         <span class="lead-status active ml-3">
             <i class="fas fa-circle"></i>
             Processing
         </span>
     @break

     @case(2)
         <span class="lead-status interested ml-3">
             <i class="fas fa-circle"></i>
             Interested
         </span>
     @break

     @case(3)
         <span class="lead-status callback ml-3">
             <i class="fas fa-circle"></i>
             Call Back
         </span>
     @break

     @case(4)
         <span class="lead-status no-answer ml-3">
             <i class="fas fa-circle"></i>
             No Answer
         </span>
     @break

     @case(5)
         <span class="lead-status not-interested ml-3">
             <i class="fas fa-circle"></i>
             Not Interested
         </span>
     @break

     @case(6)
         <span class="lead-status meeting ml-3">
             <i class="fas fa-circle"></i>
             Meeting Scheduled
         </span>
     @break

     @case(7)
         <span class="lead-status proposal ml-3">
             <i class="fas fa-circle"></i>
             Proposal Sent
         </span>
     @break

     @case(8)
         <span class="lead-status negotiation ml-3">
             <i class="fas fa-circle"></i>
             Negotiation
         </span>
     @break

     @case(9)
         <span class="lead-status converted ml-3">
             <i class="fas fa-circle"></i>
             Converted
         </span>
     @break

     @case(10)
         <span class="lead-status lost ml-3">
             <i class="fas fa-circle"></i>
             Lost
         </span>
     @break

     @case(11)
         <span class="lead-status others ml-3">
             <i class="fas fa-circle"></i>
             Others
         </span>
     @break

     @default
         <span class="lead-status pending ml-3">
             <i class="fas fa-circle"></i>
             Pending
         </span>
 @endswitch
