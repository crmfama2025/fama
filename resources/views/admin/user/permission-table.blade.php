<div class="form-group row">

    <div class="table-responsive permission-table-wrapper" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered permission-table" style="position: sticky;">
            <thead style="width: 100%">
                <tr>
                    <th></th>
                    <th>All</th>
                    @foreach ($subModules as $sub)
                        <th>{{ ucfirst(str_replace('_', ' ', $sub)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody style="width: 100%">
                @foreach ($permissions as $module)
                    @if ($module->permission_name == 'Property_type')
                        @continue
                    @endif
                    <tr>
                        <td>{{ $module->permission_name }}</td>
                        <td>
                            <div class="icheck-success d-inline">
                                <input type="checkbox" id="{{ $module->permission_name }}" class="masterParent"
                                    name="permission_id[]" data-row="{{ $module->id }}" value="{{ $module->id }}"
                                    {{ in_array($module->id, $userPermissionIds) ? 'checked' : '' }}>
                                <label class="labelpermission" for="{{ $module->permission_name }}">
                                </label>
                            </div>
                        </td>
                        @foreach ($subModules as $sub)
                            <td>
                                @foreach ($module->children as $child)
                                    @if ($child->getSubmoduleName() == $sub)
                                        <div class="icheck-success d-inline">
                                            <input type="checkbox"
                                                id="{{ $module->permission_name }}{{ $child->getSubmoduleName() }}"
                                                class="masterChild{{ $module->id }}" name="permission_id[]"
                                                value="{{ $child->id }}"
                                                {{ in_array($child->id, $userPermissionIds) ? 'checked' : '' }}>
                                            <label class="labelpermission"
                                                for="{{ $module->permission_name }}{{ $child->getSubmoduleName() }}">

                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
