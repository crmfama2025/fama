<script>
    const calendar = new FullCalendar.Calendar(document.getElementById('leadCalendar'), {
        initialView: 'dayGridMonth',
        height: 550, // fixed height, rows shrink to fit (replaces 'auto')
        fixedWeekCount: false, // no extra empty 6th week
        dayMaxEvents: 2, // show "+2 more" instead of stretching the cell
        defaultTimedEventDuration: '00:01',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek' // dropped timeGridDay to declutter
        },
        eventDisplay: 'block',
        eventColor: '#e7f1ff',
        eventBorderColor: '#b6d4fe',
        eventTextColor: '#084298',
        eventClassNames: function(arg) {
            return arg.event.extendedProps.isFollowedUp ? ['fu-done'] : [];
        },
        events: {
            url: "{{ route('lead.calendar-events') }}",
            extraParams: function() {
                return {
                    // assigned_to: $('#followedUpBySelect').val()
                };
            }
        },
        eventTimeFormat: {
            hour: 'numeric',
            minute: '2-digit',
            meridiem: 'short'
        },
        eventClick: function(info) {
            const props = info.event.extendedProps;
            const escapeHtml = value => $('<div>').text(value ?? '').html();

            const nextFollowUp = info.event.start ?
                moment(info.event.start).format('DD MMM YYYY, hh:mm A') :
                'Not set';

            Swal.fire({
                title: escapeHtml(info.event.title),
                width: '600px',
                html: `
                        <div style="text-align: left;">
                            <p><strong>Next follow-up:</strong> ${nextFollowUp}</p>
                            <p><strong>Assigned to:</strong> ${escapeHtml(props.assignedTo)}</p>
                            <p><strong>Last follow-up type:</strong> ${escapeHtml(props.type)}</p>
                            <hr>


                                <strong>Notes:</strong><br>
                                ${escapeHtml(props.notes || 'No notes')}
                        </div>
                    `,
                confirmButtonText: 'Close'
            });
        },
        eventsSet: function(events) {
            document
                .querySelectorAll(
                    '#leadCalendar .fc-daygrid-day.has-followup, #leadCalendar .fc-daygrid-day.is-overdue'
                )
                .forEach(day => day.classList.remove('has-followup', 'is-overdue'));

            events.forEach(event => {
                if (!event.start) return;

                const date = event.startStr.slice(0, 10);
                const dayCell = document.querySelector(
                    `#leadCalendar .fc-daygrid-day[data-date="${date}"]`
                );

                if (!dayCell) return;

                dayCell.classList.add('has-followup');

                const notFollowedUp = !event.extendedProps.isFollowedUp;
                const overdue = moment(date).isBefore(moment().startOf('day'));

                if (overdue && notFollowedUp) {
                    dayCell.classList.add('is-overdue');
                }
            });
        },
        eventMouseEnter: function(info) {
            const props = info.event.extendedProps;
            const tooltip = document.createElement('div');
            tooltip.className = 'followup-tooltip';

            const addRow = (label, value) => {
                const row = document.createElement('div');
                row.className = 'followup-tooltip__row';

                const labelEl = document.createElement('span');
                labelEl.className = 'followup-tooltip__label';
                labelEl.textContent = label;

                const valueEl = document.createElement('span');
                valueEl.className = 'followup-tooltip__value';
                valueEl.textContent = value || 'Not set';

                row.append(labelEl, valueEl);
                tooltip.appendChild(row);
            };

            const title = document.createElement('div');
            title.className = 'followup-tooltip__title';
            title.textContent = info.event.title || 'Follow-up';
            tooltip.appendChild(title);

            addRow(
                'Next follow-up',
                info.event.start ?
                moment(info.event.start).format('DD MMM YYYY, hh:mm A') :
                'Not set'
            );
            addRow('Assigned to', props.assignedTo);
            addRow('Last follow-up type', props.type);

            const notes = document.createElement('div');
            notes.className = 'followup-tooltip__notes';

            const notesLabel = document.createElement('div');
            notesLabel.className = 'followup-tooltip__label';
            notesLabel.textContent = 'Notes';

            const notesText = document.createElement('div');
            notesText.className = 'followup-tooltip__notes-text';
            notesText.textContent = props.notes || 'No notes';

            notes.append(notesLabel, notesText);
            tooltip.appendChild(notes);

            document.body.appendChild(tooltip);

            // Keep the tooltip inside the visible browser window.
            const eventRect = info.el.getBoundingClientRect();
            const tipRect = tooltip.getBoundingClientRect();
            const margin = 8;

            let left = eventRect.right + margin;
            if (left + tipRect.width > window.innerWidth - margin) {
                left = eventRect.left - tipRect.width - margin;
            }
            left = Math.max(margin, Math.min(left, window.innerWidth - tipRect.width - margin));

            let top = eventRect.top;
            top = Math.max(margin, Math.min(top, window.innerHeight - tipRect.height - margin));

            tooltip.style.left = `${left}px`;
            tooltip.style.top = `${top}px`;
            info.el._followUpTooltip = tooltip;
        },

        eventMouseLeave: function(info) {
            info.el._followUpTooltip?.remove();
            delete info.el._followUpTooltip;
        },
    });

    calendar.render();

    $('#followedUpBySelect').on('change', function() {
        calendar.refetchEvents();
    });
    $('#calendarViewBtn').on('click', function() {
        alert("test");
        $('#listCard').hide();
        $('#calendarCard').show();
        calendar.updateSize(); // needed after un-hiding
        $(this).removeClass('btn-default').addClass('btn-primary');
        $('#listViewBtn').removeClass('btn-primary').addClass('btn-default');
    });

    $('#listViewBtn').on('click', function() {
        $('#calendarCard').hide();
        $('#listCard').show();
        $(this).removeClass('btn-default').addClass('btn-primary');
        $('#calendarViewBtn').removeClass('btn-primary').addClass('btn-default');
    });
</script>
