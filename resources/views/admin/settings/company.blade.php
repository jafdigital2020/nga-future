@extends('layouts.settings') @section('title', 'One JAF')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')


<!-- Page Content -->
<div class="content container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Company</h3>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <form action="{{ route('company.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Company Name <span class="text-danger">*</span></label>
                            <input class="form-control" name="company" id="company" type="text"
                                value="{{ $company ? $company->company : '' }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Contact Person</label>
                            <input class="form-control" name="contactPerson" id="contactPerson" type="text"
                                value="{{ $company ? $company->contactPerson : '' }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Address</label>
                            <input class="form-control" name="comAddress" id="comAddress"
                                value="{{ $company ? $company->comAddress : '' }}" type="text">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label>Country</label>
                            <input class="form-control" name="country" id="country"
                                value="{{ $company ? $company->country : '' }}" type="text">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label>State/Province</label>
                            <input class="form-control" name="province" id="province"
                                value="{{ $company ? $company->province : '' }}" type="text">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label>City</label>
                            <input class="form-control" name="city" id="city"
                                value="{{ $company ? $company->city : '' }}" type="text">
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-6 col-lg-3">
                        <div class="form-group">
                            <label>Postal Code</label>
                            <input class="form-control" name="postalCode" id="postalCode"
                                value="{{ $company ? $company->postalCode : '' }}" type="text">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input class="form-control" value="{{ $company ? $company->comEmail : '' }}" name="comEmail"
                                id="comEmail" type="email">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input class="form-control" value="{{ $company ? $company->comPhone : '' }}" name="comPhone"
                                id="comPhone" type="text">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input class="form-control" value="{{ $company ? $company->comMobile : '' }}"
                                name="comMobile" id="comMobile" type="text">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Fax</label>
                            <input class="form-control" value="{{ $company ? $company->comFax : '' }}" name="comFax"
                                id="comFax" type="text">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Website Url</label>
                            <input class="form-control" value="{{ $company ? $company->comWebsite : '' }}"
                                name="comWebsite" id="comWebsite" type="text">
                        </div>
                    </div>
                </div>
                <div class="submit-section">
                    <button type="submit" class="btn btn-primary submit-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Page Content -->

@endsection

@section('scripts')

<!-- <script>
    const data = {
        Philippines: {
            provinces: {
                NCR: ['Manila', 'Quezon City', 'Makati'],
                Cavite: ['Bacoor', 'Dasmariñas', 'Imus'],
                Laguna: ['Calamba', 'Santa Rosa', 'San Pedro']
            }
        }
    };

    function updateProvinces() {
        const countrySelect = document.getElementById('country');
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        const selectedCountry = countrySelect.value;

        // Clear previous options
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        citySelect.innerHTML = '<option value="">Select City</option>';

        if (selectedCountry && data[selectedCountry]) {
            const provinces = Object.keys(data[selectedCountry].provinces);
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province;
                option.textContent = province;
                provinceSelect.appendChild(option);
            });
        }
    }

    function updateCities() {
        const countrySelect = document.getElementById('country');
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        const selectedCountry = countrySelect.value;
        const selectedProvince = provinceSelect.value;

        // Clear previous options
        citySelect.innerHTML = '<option value="">Select City</option>';

        if (selectedCountry && selectedProvince && data[selectedCountry]) {
            const cities = data[selectedCountry].provinces[selectedProvince];
            cities.forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
        }
    }

</script> -->

@endsection
