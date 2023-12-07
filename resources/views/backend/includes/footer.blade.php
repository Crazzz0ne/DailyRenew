<footer class="app-footer">
    <div>
        <strong>@lang('labels.general.copyright') &copy; {{ date('Y') }}

        </strong> @lang('strings.backend.general.all_rights_reserved')
        <span>SCES</span>
    </div>
    <!-- Button trigger modal -->

    <!-- Modal -->
    <div class="modal fade" id="submitIssue" tabindex="-1" role="dialog" aria-labelledby="submitIssueLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 800px;">
            <div class="modal-content">
                <form action="{{ route('dashboard.support.store') }}" method="POST"
                      enctype="multipart/form-data">
                    @CSRF
                    <div class="modal-header">
                        <h5 class="modal-title" id="submitIssueLabel">Support</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p>Having a problem wanna let us know?</p>
                        <label for="subject">Subject</label><br>
                        <input type="text" name="subject" id="subject" class="form-control">
                        <div class="py-2">
                            <label for="body">Please be as detailed as you can</label>
                            <textarea class="form-control" id="body" rows="3" name="body"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="ml-auto">
        <button type="button" id="submitIssueButton" class="btn btn-sm btn-primary" data-toggle="modal"
                data-target="#submitIssue">Submit an
            Issue
        </button>
    </div>
</footer>
