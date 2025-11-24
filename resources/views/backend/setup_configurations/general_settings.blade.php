@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h1 class="mb-0 h6">{{translate('General Settings')}}</h1>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label">{{translate('System Name')}}</label>
                            <div class="col-sm-9">
                                <input type="hidden" name="types[]" value="site_name">
                                <input type="text" name="site_name" class="form-control" value="{{ get_setting('site_name') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label">{{translate('System Logo - White')}}</label>
                            <div class="col-sm-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose Files') }}</div>
                                    <input type="hidden" name="types[]" value="system_logo_white">
                                    <input type="hidden" name="system_logo_white" value="{{ get_setting('system_logo_white') }}" class="selected-files">
                                </div>
                                <div class="file-preview box sm"></div>
                                <small>{{ translate('Will be used in admin panel side menu') }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label">{{translate('System Logo - Black')}}</label>
                            <div class="col-sm-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose Files') }}</div>
                                    <input type="hidden" name="types[]" value="system_logo_black">
                                    <input type="hidden" name="system_logo_black" value="{{ get_setting('system_logo_black') }}" class="selected-files">
                                </div>
                                <div class="file-preview box sm"></div>
                                <small>{{ translate('Will be used in admin panel topbar in mobile + Admin login page') }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label">{{translate('System Timezone')}}</label>
                            <div class="col-sm-9">
                                <input type="hidden" name="types[]" value="timezone">
                                <select name="timezone" class="form-control aiz-selectpicker" data-live-search="true">
                                    @foreach (timezones() as $key => $value)
                                        <option value="{{ $value }}" @if (app_timezone() == $value)
                                            selected
                                        @endif>{{ $key }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label">{{translate('Admin login page background')}}</label>
                            <div class="col-sm-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose Files') }}</div>
                                    <input type="hidden" name="types[]" value="admin_login_background">
                                    <input type="hidden" name="admin_login_background" value="{{ get_setting('admin_login_background') }}" class="selected-files">
                                </div>
                                <div class="file-preview box sm"></div>
                            </div>
                        </div>
                        
                        <!-- Droploo API Credentials -->
                        <div class="card border shadow-none mt-4">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Droploo API Credentials') }}</h5>
                                <p class="text-muted mb-0">{{ translate('Configure your Droploo API connection settings') }}</p>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-from-label">{{translate('Droploo Username')}}</label>
                                    <div class="col-sm-9">
                                        <input type="hidden" name="types[]" value="droploo_username">
                                        <input type="text" name="droploo_username" class="form-control" value="{{ get_setting('droploo_username') }}" placeholder="Enter Droploo Username">
                                        <small class="form-text text-muted">{{ translate('Your Droploo account username') }}</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-from-label">{{translate('Droploo App Key')}}</label>
                                    <div class="col-sm-9">
                                        <input type="hidden" name="types[]" value="droploo_app_key">
                                        <input type="text" name="droploo_app_key" class="form-control" value="{{ get_setting('droploo_app_key') }}" placeholder="Enter Droploo App Key">
                                        <small class="form-text text-muted">{{ translate('API key provided by Droploo') }}</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-from-label">{{translate('Droploo App Secret')}}</label>
                                    <div class="col-sm-9">
                                        <input type="hidden" name="types[]" value="droploo_app_secret">
                                        <div class="input-group">
                                            <input type="password" name="droploo_app_secret" class="form-control" value="{{ get_setting('droploo_app_secret') }}" placeholder="Enter Droploo App Secret" id="droploo_app_secret">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('droploo_app_secret')">
                                                    <i class="lar la-eye" id="toggleIcon"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">{{ translate('API secret provided by Droploo') }}</small>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="las la-info-circle"></i> {{ translate('After updating these credentials, you can manage Droploo products from the Products section.') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right">
    						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
    					</div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script type="text/javascript">
    function togglePasswordVisibility(inputId) {
        var passwordInput = document.getElementById(inputId);
        var toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('lar', 'la-eye');
            toggleIcon.classList.add('las', 'la-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('las', 'la-eye-slash');
            toggleIcon.classList.add('lar', 'la-eye');
        }
    }
</script>
@endsection
