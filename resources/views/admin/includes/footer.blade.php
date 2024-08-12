<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Your Website 2020</span>
        </div>
    </div>
</footer>
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="{{url('admin.logout')}}">Logout</a>
            </div>
        </div>
    </div>
</div>


<!-- Vertically centered modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form   action="{{ url('student.changesPassword', Auth::guard('student')->user()->id) }}" method="POST">
            @csrf
        <div class="modal-body">
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label for="validationDefault01">Old Password</label>
                    <input type="password" class="form-control" id="validationDefault01" name="change password">
                  </div>
                  <div class="col-md-12 mb-3">
                    <label for="validationDefault02">New Password</label>
                    <input type="password" class="form-control" id="validationDefault02" name="new password">
                  </div>
                  <div class="col-md-12 mb-3">
                    <label for="validationDefault03">Confirm Password</label>
                    <input type="password" class="form-control" id="validationDefault03" name="confirm password">
                </div>
                </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </div>
    </form>

      </div>
    </div>
  </div>
</div>
