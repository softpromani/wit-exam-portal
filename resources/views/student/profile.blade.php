@extends('student.includes.master')

@section('style_area')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
<style>
    /* Image Cropper Modal & Controls Styling */
    #imageCropperModal {
        z-index: 1070 !important;
    }
    .modal-backdrop.cropper-backdrop {
        z-index: 1065 !important;
    }
    .cropper-view-box,
    .cropper-face {
        border-radius: 0;
    }
    .cropper-preview-container {
        overflow: hidden;
        background: #f8f9fa;
        border: 2px dashed #4e73df;
        border-radius: 6px;
        margin: 0 auto;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 120, 0.15);
    }
    .img-container-wrapper {
        min-height: 350px;
        max-height: 450px;
        background-color: #121212;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .cropper-wrapper img {
        max-width: 100%;
        max-height: 420px;
        display: block;
    }
    .crop-preview-thumb {
        border: 2px solid #e3e6f0;
        border-radius: 6px;
        padding: 3px;
        background: #fff;
        transition: all 0.2s ease-in-out;
    }
    .crop-preview-thumb:hover {
        border-color: #4e73df;
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
    <h1 class="h3 mb-0 text-gray-800">Student Profile</h1>
    @if($editstudent->is_profile == 1)
        <span class="badge badge-success px-3 py-2 shadow-sm font-weight-bold" style="font-size: 90%;">
            <i class="fas fa-lock mr-1"></i> Profile Locked (Verified)
        </span>
    @else
        <span class="badge badge-warning text-dark px-3 py-2 shadow-sm font-weight-bold" style="font-size: 90%;">
            <i class="fas fa-unlock-alt mr-1"></i> Profile Unlocked for Editing
        </span>
    @endif
</div>

@if($editstudent->is_profile == 0)
    <div class="alert alert-warning border-left-warning shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <i class="fas fa-unlock-alt fa-2x text-warning"></i>
            </div>
            <div>
                <h5 class="alert-heading font-weight-bold mb-1">Profile Unlocked for Editing</h5>
                <p class="mb-0 text-dark">Your profile has been unlocked by the administrator. You can update your details below and click <strong>Update Profile</strong> to save your changes.</p>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info border-left-info shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <i class="fas fa-lock fa-2x text-info"></i>
            </div>
            <div>
                <h5 class="alert-heading font-weight-bold mb-1">Profile Locked</h5>
                <p class="mb-0">Your profile information is verified and locked. If you need to make any changes or corrections, please request the administrator to unlock your profile.</p>
            </div>
        </div>
    </div>
@endif

<div class="container card shadow-sm">

    <div class="card-body">

        <form action="{{($editstudent->is_profile==0)?route('student.store'):'#'}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-6 mb-3">
                    <label for="enrollment_number" class="form-label">University Roll No</label>
                    <input type="text" class="form-control" id="enrollment_number" name="enrollment_number"
                        value="{{ isset($editstudent) ?$editstudent->university_roll_no :'' }}" required  readonly/>
                </div>
                @error('enrollment_number')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="reg_number" class="form-label">Registration Number</label>
                    <input type="text" class="form-control" id="reg_numbr" name="reg_number"
                        value="{{ isset($editstudent) ?$editstudent->registration_no :  '' }}" required readonly>
                </div>
                @error('reg_number')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="student_name"
                        placeholder="Enter your name"
                        value="{{ isset($editstudent) ?$editstudent->student_name :  old('student_name') }}">
                </div>
                @error('student_name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-control" name="gender">
                        <option value="">-- Select Gender --</option>
                        <option value="male" {{ isset($editstudent) && $editstudent->gender == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ isset($editstudent) && $editstudent->gender == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="transgender" {{ isset($editstudent) && $editstudent->gender == 'transgender' ? 'selected' : '' }}>Transgender</option>
                    </select>
                </div>
                @error('gender')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="dob" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob"
                           placeholder="Select your date of birth"
                           value="{{ isset($editstudent) ? $editstudent->dob : old('dob') }}">
                </div>
                @error('dob')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="adhar_number" class="form-label">Adhar Number</label>
                    <input type="number" class="form-control" id="adhar_number" name="adhar_number"
                           placeholder="Enter Adhar Number"
                           value="{{ isset($editstudent) ? $editstudent->adhar_number : old('adhar_number') }}">
                </div>
                @error('dob')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"
                        value="{{ isset($editstudent) ? $editstudent->email : old('email') }}">
                </div>
                @error('email')
                <span class="text-danger">{{ $message }}</span>
                @enderror

                <div class="col-6 mb-3">
                    <label for="mobile" class="form-label">Mobile No</label>
                    <input type="number" class="form-control" id="mobile_no" name="mobile_number"
                        placeholder="Enter your mobile_no"
                        value="{{ isset($editstudent) ? $editstudent->mobile_number :  old('mobile_number') }}">
                </div>
                @error('mobile_number')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="fname" class="form-label">Father name</label>
                    <input type="text" class="form-control" id="fname" name="fname"
                        placeholder="Enter your father name"
                        value="{{ isset($editstudent) ?$editstudent->fname :  old('name') }}">
                </div>
                @error('fname')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="mname" class="form-label">Mother Name</label>
                    <input type="text" class="form-control" id="mname" name="mname"
                        placeholder="Enter your name"
                        value="{{ isset($editstudent) ?$editstudent->mname :  old('mname') }}">
                </div>
                @error('mname')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="parent_number" class="form-label">Parent/Guardian Contact No.</label>
                    <input type="text" class="form-control" placeholder="Enter Parent/Guardian Number" id="parent_number" name="parent_number"
                        value="{{ isset($editstudent) ? $editstudent->parent_number  : '' }}">
                        @error('parent_number')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                <div class="col-6 mb-3">
                    <label for="semester" class="form-label">Current Semester</label>
                    <input type="text" class="form-control" id="semester" name="semester"
                        value="{{ isset($editstudent) ? $editstudent->semester_id  : '' }}" readonly>
                </div>
                @error('parent_number')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-12 mb-3">
                    <label for="enrollment_number" class="form-label">Address</label>
                    <textarea class="form-control" name="address"> {{ isset($editstudent) ?$editstudent->address:'' }}</textarea>

                </div>
                @if(!isset($editstudent->profile_pic))
                <div class="col-6 mb-3">
                    <label for="photo" class="form-label font-weight-bold">Upload Picture <span class="badge badge-info ml-1">Auto-crops to 300x400 px</span></label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/jpeg,image/png,image/jpg,image/webp"/>
                    <small class="form-text text-muted">Select an image to crop and resize (must be less than 512 Kb).</small>
                    <div class="mt-2" id="profilePreviewBox" style="display: none;">
                        <span class="badge badge-success mb-1"><i class="fas fa-check-circle mr-1"></i> Cropped Photo (300x400 px):</span>
                        <div>
                            <img id="profilePreview" src="" class="crop-preview-thumb shadow-sm" style="width: 150px; height: 200px; object-fit: cover;" />
                        </div>
                    </div>
                    @error('photo')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                @else
                <div class="col-6 mb-3">
                    <b>Your Profile Pic</b> <br>
                    <img src="{{asset('storage/'.$editstudent->profile_pic->media)}}" alt="Profile Pic" class="crop-preview-thumb mt-1 mb-2" style="width: 150px; height: 200px; object-fit: cover;">
                    <br>
                    <button class="btn btn-outline-danger btn-sm font-weight-bold" type="button" data-toggle="modal" data-target="#profilePicModal" {{ ($editstudent->is_profile == 1) ? 'disabled' : '' }} title="{{ ($editstudent->is_profile == 1) ? 'Profile is locked' : 'Change picture' }}">
                        <i class="fas fa-camera mr-1"></i> Change Pic
                    </button>
                    @if($editstudent->is_profile == 1)
                        <small class="d-block text-muted mt-1"><i class="fas fa-lock mr-1"></i> Locked</small>
                    @endif
                </div>

                @endif

                @if(!isset($editstudent->sign))
                <div class="col-6 mb-3">
                    <label for="signature" class="form-label font-weight-bold">Signature <span class="badge badge-info ml-1">Auto-crops to 200x100 px</span></label>
                    <input type="file" class="form-control" id="signature" name="signature" accept="image/jpeg,image/png,image/jpg,image/webp"/>
                    <small class="form-text text-muted">Select a signature image to crop and resize (must be less than 512 Kb).</small>
                    <div class="mt-2" id="signaturePreviewBox" style="display: none;">
                        <span class="badge badge-success mb-1"><i class="fas fa-check-circle mr-1"></i> Cropped Signature (200x100 px):</span>
                        <div>
                            <img id="signaturePreview" src="" class="crop-preview-thumb shadow-sm" style="width: 200px; height: 100px; object-fit: contain; background: #fff;" />
                        </div>
                    </div>
                    @error('signature')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                @else
                    <div class="col-6 mb-3">
                        <b>Your Signature</b><br>
                        <img src="{{asset('storage/'.$editstudent->sign->media)}}" alt="Signature" class="crop-preview-thumb mt-1 mb-2" style="width: 200px; height: 100px; object-fit: contain; background: #fff;">
                        <br>
                        <button class="btn btn-outline-danger btn-sm font-weight-bold" type="button" data-toggle="modal" data-target="#SignModal" {{ ($editstudent->is_profile == 1) ? 'disabled' : '' }} title="{{ ($editstudent->is_profile == 1) ? 'Profile is locked' : 'Change signature' }}">
                            <i class="fas fa-signature mr-1"></i> Change Signature
                        </button>
                        @if($editstudent->is_profile == 1)
                            <small class="d-block text-muted mt-1"><i class="fas fa-lock mr-1"></i> Locked</small>
                        @endif
                    </div>
                @endif

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary font-weight-bold shadow-sm" {{($editstudent->is_profile==1)?'disabled':''}} >
                        <i class="fas fa-save mr-1"></i> Update Profile
                    </button>
                    @if($editstudent->is_profile == 1)
                        <span class="ml-2 text-muted small"><i class="fas fa-info-circle mr-1"></i> Profile locked by administration.</span>
                    @endif
                </div>
            </div>
        </form>
    </div>

</div>

{{-- Profile Pic Modal --}}
<div class="modal fade" id="profilePicModal" tabindex="-1" aria-labelledby="profilePicModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow">
        <form action="{{ route('student.file-update') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold" id="profilePicModalLabel"><i class="fas fa-user-circle mr-2"></i>Update Profile Photo</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="upload_id" value="{{ $editstudent->profile_pic?->id }}" required>
                <input type="hidden" name="type" value="photo" required>
                
                <div class="form-group">
                    <label for="modal_profile_pic" class="form-label font-weight-bold">Select New Photo <span class="badge badge-info ml-1">Auto-crops to 300x400 px</span></label>
                    <input type="file" name="pic" id="modal_profile_pic" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp" required>
                    <small class="form-text text-muted">Choosing an image will automatically open the cropper tool.</small>
                    @error('pic')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-3 text-center" id="modalProfilePreviewBox" style="display: none;">
                    <span class="badge badge-success mb-2"><i class="fas fa-check-circle mr-1"></i> Cropped Preview (300x400 px)</span>
                    <div>
                        <img id="modalProfilePreview" src="" class="crop-preview-thumb shadow-sm" style="width: 150px; height: 200px; object-fit: cover;">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary font-weight-bold">Save changes</button>
            </div>
        </form>
      </div>
    </div>
</div>

{{-- Signature Modal --}}
<div class="modal fade" id="SignModal" tabindex="-1" aria-labelledby="SignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow">
        <form action="{{ route('student.file-update') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold" id="SignModalLabel"><i class="fas fa-pen-nib mr-2"></i>Update Signature</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="upload_id" value="{{ $editstudent->sign?->id }}" required>
                <input type="hidden" name="type" value="sign" required>
                
                <div class="form-group">
                    <label for="modal_signature_pic" class="form-label font-weight-bold">Select New Signature <span class="badge badge-info ml-1">Auto-crops to 200x100 px</span></label>
                    <input type="file" name="pic" id="modal_signature_pic" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp" required>
                    <small class="form-text text-muted">Choosing a signature will automatically open the cropper tool.</small>
                    @error('pic')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-3 text-center" id="modalSignPreviewBox" style="display: none;">
                    <span class="badge badge-success mb-2"><i class="fas fa-check-circle mr-1"></i> Cropped Preview (200x100 px)</span>
                    <div>
                        <img id="modalSignPreview" src="" class="crop-preview-thumb shadow-sm" style="width: 200px; height: 100px; object-fit: contain; background: #fff;">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary font-weight-bold">Save changes</button>
            </div>
        </form>
      </div>
    </div>
</div>

{{-- Interactive Image Cropper Modal --}}
<div class="modal fade" id="imageCropperModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title font-weight-bold d-flex align-items-center" id="cropperModalTitle">
                    <i class="fas fa-crop-alt mr-2 text-primary"></i>
                    <span id="cropperTitleText">Crop & Resize Image</span>
                    <span class="badge badge-primary ml-2 py-1 px-2" id="cropperDimensionsBadge" style="font-size: 0.8rem;"></span>
                </h5>
                <button type="button" class="close text-white" id="btnCloseCropperX" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="row">
                    <!-- Left Column: Cropper Container & Toolbar -->
                    <div class="col-lg-8 col-md-7 mb-3 mb-md-0">
                        <div class="img-container-wrapper shadow-sm">
                            <img id="cropperSourceImage" src="" alt="Crop Source">
                        </div>
                        <!-- Controls Toolbar -->
                        <div class="d-flex flex-wrap justify-content-center align-items-center mt-3 pt-2 border-top">
                            <div class="btn-group mr-2 mb-2" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnZoomIn" title="Zoom In">
                                    <i class="fas fa-search-plus mr-1"></i> Zoom In
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnZoomOut" title="Zoom Out">
                                    <i class="fas fa-search-minus mr-1"></i> Zoom Out
                                </button>
                            </div>
                            <div class="btn-group mr-2 mb-2" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRotateLeft" title="Rotate Left">
                                    <i class="fas fa-undo mr-1"></i> Rotate Left
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRotateRight" title="Rotate Right">
                                    <i class="fas fa-redo mr-1"></i> Rotate Right
                                </button>
                            </div>
                            <button type="button" class="btn btn-outline-warning btn-sm mb-2" id="btnResetCrop" title="Reset Crop">
                                <i class="fas fa-sync-alt mr-1"></i> Reset
                            </button>
                        </div>
                    </div>
                    <!-- Right Column: Live Preview & Guidance -->
                    <div class="col-lg-4 col-md-5 d-flex flex-column justify-content-between">
                        <div class="card bg-light border-0 shadow-sm p-3 mb-3">
                            <h6 class="font-weight-bold text-dark text-center mb-2">Live Preview</h6>
                            <div class="d-flex justify-content-center align-items-center p-2 bg-white rounded border" style="min-height: 220px;">
                                <div id="cropperLivePreview" class="cropper-preview-container"></div>
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted font-weight-bold" id="previewDimensionLabel">Target: 300x400 px</small>
                            </div>
                        </div>
                        <div class="alert alert-info py-2 px-3 mb-0 small">
                            <strong class="d-block mb-1"><i class="fas fa-info-circle mr-1"></i> Instructions:</strong>
                            <ul class="pl-3 mb-0">
                                <li>Drag corners to fit desired area.</li>
                                <li>Use mouse wheel or buttons to zoom.</li>
                                <li>Automatically resized to exact required dimensions & under 512 Kb.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary px-3" id="btnCancelCropper">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-success px-4 font-weight-bold shadow-sm" id="btnApplyCropper">
                    <i class="fas fa-check mr-1"></i> Crop & Apply
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script_section')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
$(document).ready(function () {
    let currentCropper = null;
    let currentTargetInput = null;
    let currentPreviewImg = null;
    let currentPreviewBox = null;
    let currentTargetConfig = null;
    let currentParentModal = null;
    let currentFileName = 'cropped_image.jpg';

    const CROP_CONFIGS = {
        photo: {
            title: 'Crop & Resize Profile Photo',
            aspectRatio: 300 / 400,
            targetWidth: 300,
            targetHeight: 400,
            previewWidth: 150,
            previewHeight: 200,
            dimensionText: '300 × 400 px (3:4 ratio)',
            mimeType: 'image/jpeg'
        },
        sign: {
            title: 'Crop & Resize Signature',
            aspectRatio: 200 / 100,
            targetWidth: 200,
            targetHeight: 100,
            previewWidth: 200,
            previewHeight: 100,
            dimensionText: '200 × 100 px (2:1 ratio)',
            mimeType: 'image/jpeg'
        }
    };

    function setupCropperInput(inputSelector, type, previewImgSelector, previewBoxSelector, parentModalSelector) {
        $(inputSelector).on('change', function (e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            if (!file.type.match(/^image\/(jpeg|png|jpg|webp)$/i)) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Please choose a valid image file (JPG, PNG, WebP).');
                } else {
                    alert('Please choose a valid image file (JPG, PNG, WebP).');
                }
                $(this).val('');
                return;
            }

            currentTargetInput = this;
            currentTargetConfig = CROP_CONFIGS[type];
            currentPreviewImg = previewImgSelector ? $(previewImgSelector)[0] : null;
            currentPreviewBox = previewBoxSelector ? $(previewBoxSelector)[0] : null;
            currentParentModal = parentModalSelector ? $(parentModalSelector) : null;
            currentFileName = file.name ? file.name.replace(/\.[^/.]+$/, "") + ".jpg" : "cropped_" + type + ".jpg";

            const reader = new FileReader();
            reader.onload = function (event) {
                openCropperModal(event.target.result);
            };
            reader.readAsDataURL(file);
        });
    }

    function openCropperModal(imageUrl) {
        $('#cropperTitleText').text(currentTargetConfig.title);
        $('#cropperDimensionsBadge').text(currentTargetConfig.dimensionText);
        $('#previewDimensionLabel').text('Target Size: ' + currentTargetConfig.dimensionText);

        $('#cropperLivePreview').css({
            width: currentTargetConfig.previewWidth + 'px',
            height: currentTargetConfig.previewHeight + 'px'
        });

        $('#cropperSourceImage').attr('src', imageUrl);
        $('#imageCropperModal').modal('show');
    }

    $('#imageCropperModal').on('shown.bs.modal', function () {
        if (currentCropper) {
            currentCropper.destroy();
            currentCropper = null;
        }

        const image = document.getElementById('cropperSourceImage');
        currentCropper = new Cropper(image, {
            aspectRatio: currentTargetConfig.aspectRatio,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.95,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
            preview: '#cropperLivePreview',
        });
    });

    $('#imageCropperModal').on('hidden.bs.modal', function () {
        if (currentCropper) {
            currentCropper.destroy();
            currentCropper = null;
        }
        $('#cropperSourceImage').attr('src', '');

        // Maintain backdrop and scroll state when parent modal is active
        if (currentParentModal && currentParentModal.hasClass('show')) {
            $('body').addClass('modal-open');
        }
    });

    // Toolbar Buttons
    $('#btnZoomIn').on('click', function () {
        if (currentCropper) currentCropper.zoom(0.1);
    });
    $('#btnZoomOut').on('click', function () {
        if (currentCropper) currentCropper.zoom(-0.1);
    });
    $('#btnRotateLeft').on('click', function () {
        if (currentCropper) currentCropper.rotate(-90);
    });
    $('#btnRotateRight').on('click', function () {
        if (currentCropper) currentCropper.rotate(90);
    });
    $('#btnResetCrop').on('click', function () {
        if (currentCropper) currentCropper.reset();
    });

    // Cancel Button
    function handleCancelCrop() {
        if (currentTargetInput && (!currentTargetInput.files || currentTargetInput.files.length === 0)) {
            $(currentTargetInput).val('');
        }
        $('#imageCropperModal').modal('hide');
    }
    $('#btnCancelCropper, #btnCloseCropperX').on('click', handleCancelCrop);

    // Apply Cropped Image
    $('#btnApplyCropper').on('click', function () {
        if (!currentCropper) return;

        // Generate cropped canvas at exact dimensions
        const rawCanvas = currentCropper.getCroppedCanvas({
            width: currentTargetConfig.targetWidth,
            height: currentTargetConfig.targetHeight,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        if (!rawCanvas) {
            if (typeof toastr !== 'undefined') toastr.error('Could not crop the image.');
            return;
        }

        // Fill background with solid white to prevent transparent PNG signatures turning black
        const finalCanvas = document.createElement('canvas');
        finalCanvas.width = currentTargetConfig.targetWidth;
        finalCanvas.height = currentTargetConfig.targetHeight;
        const ctx = finalCanvas.getContext('2d');
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, finalCanvas.width, finalCanvas.height);
        ctx.drawImage(rawCanvas, 0, 0, finalCanvas.width, finalCanvas.height);

        finalCanvas.toBlob(function (blob) {
            if (!blob) {
                if (typeof toastr !== 'undefined') toastr.error('Error processing cropped file.');
                return;
            }

            // Create File object
            const croppedFile = new File([blob], currentFileName, {
                type: currentTargetConfig.mimeType,
                lastModified: Date.now()
            });

            // Set file to input using modern DataTransfer API
            try {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                currentTargetInput.files = dataTransfer.files;
            } catch (err) {
                console.error('DataTransfer assignment error:', err);
            }

            // Update Preview Thumbnail
            const previewDataUrl = finalCanvas.toDataURL(currentTargetConfig.mimeType, 0.92);
            if (currentPreviewImg) {
                currentPreviewImg.src = previewDataUrl;
            }
            if (currentPreviewBox) {
                $(currentPreviewBox).fadeIn();
            }

            if (typeof toastr !== 'undefined') {
                toastr.success(currentTargetConfig.title + ' applied (' + currentTargetConfig.targetWidth + 'x' + currentTargetConfig.targetHeight + ' px)');
            }

            $('#imageCropperModal').modal('hide');
        }, currentTargetConfig.mimeType, 0.92);
    });

    // Initialize Cropper for all 4 inputs:
    // 1. Initial Profile Photo
    setupCropperInput('#photo', 'photo', '#profilePreview', '#profilePreviewBox', null);
    // 2. Initial Signature
    setupCropperInput('#signature', 'sign', '#signaturePreview', '#signaturePreviewBox', null);
    // 3. Update Modal Profile Photo
    setupCropperInput('#modal_profile_pic', 'photo', '#modalProfilePreview', '#modalProfilePreviewBox', '#profilePicModal');
    // 4. Update Modal Signature
    setupCropperInput('#modal_signature_pic', 'sign', '#modalSignPreview', '#modalSignPreviewBox', '#SignModal');
});
</script>
@endsection
