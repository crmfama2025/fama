@if ($allocations->count())
    <div class="info-card mb-4">
        <div class="info-card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-layer-group mr-2 text-primary"></i>
                Allocations
            </h5>

            <span class="badge badge-primary">
                {{ $allocations->count() }} Allocation{{ $allocations->count() > 1 ? 's' : '' }}
            </span>
        </div>
        {{-- @dump($lead) --}}

        <div class="info-card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="summary-item mb-0">
                        <div class="summary-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="summary-content">
                            <span class="summary-label">Required Staff</span>
                            <div class="summary-value">
                                {{ number_format($lead->total_staff ?? 0) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="summary-item mb-0">
                        <div class="summary-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="summary-content">
                            <span class="summary-label">Allocated</span>
                            <div class="summary-value">
                                {{ number_format($lead->total_allocation ?? 0) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="summary-item mb-0">
                        <div class="summary-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div class="summary-content">
                            <span class="summary-label">Remaining</span>
                            <div class="summary-value">
                                {{ number_format(max(0, ($lead->total_staff ?? 0) - ($lead->total_allocation ?? 0))) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Allocation</th>
                            <th>Staff Allocated</th>
                            <th>Created On</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($allocations as $index => $allocation)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    Allocation #{{ $index + 1 }}
                                </td>
                                <td>
                                    <strong>
                                        {{ number_format(
                                            $allocation->agreementUnits->sum(function ($agreementUnit) {
                                                return $agreementUnit->contractUnitDetail->subunitcount_per_unit ?? 0;
                                            }),
                                        ) }}
                                    </strong>
                                </td>
                                <td>
                                    {{ $allocation->created_at?->format('d M Y, h:i A') }}
                                </td>
                                <td>
                                    @if (auth()->user()->hasAnyPermission(['tenant-registration.view']))
                                        <a href="{{ route('tenant-registration.show', [$allocation->id]) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
