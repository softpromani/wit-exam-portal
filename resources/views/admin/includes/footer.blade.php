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
                <a class="btn btn-primary" href="{{route('admin.admin-logout')}}">Logout</a>
            </div>
        </div>
    </div>
</div>


<!-- Vertically changpassword modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form   action="{{ url('student.changesPassword', Auth::id()) }}" method="POST">
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


<!-- Payment  modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModal" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form   action="{{ url('student.changesPassword', Auth::id()) }}" method="POST">
          @csrf
      <div class="modal-body">
              <div class="row">
                  <div class="col-6 mb-3">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select class="form-control" name="payment_status">
                        <option value="">-- Select Payament Status --</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid" >Unpaid</option>
                        <option value="partial">Partial Paid</option>
                    </select>
                  </div>                
                  @error('payment_status')
                  <span class="text-danger">{{ $message }}</span>
                  @enderror
                <div class="col-6 mb-3">
                  <label for="payment_mode" class="form-label">Payment Mode</label>
                  <select class="form-control" name="payment_mode">
                      <option value="">-- Select Payament Mode --</option>
                      <option value="online">Online</option>
                      <option value="cash" >Cash</option>
                  </select>
              </div>                
                @error('payment_mode')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-md-6 mb-3">
                  <label for="ammount">Ammount</label>
                  <input type="text" class="form-control" id="validationDefault02" name="ammount">
                </div> 
                @error('ammount')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-md-6 mb-3">
                  <label for="transaction_id">Transcation Id</label>
                  <input type="text" class="form-control" id="validationDefault02" name="transaction_id">
                </div> 
                @error('transaction_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
           </div>

      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Payment</button>
      </div>
  </form>

    </div>
  </div>
</div>
</div>