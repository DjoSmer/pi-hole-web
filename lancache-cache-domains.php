<?php
/**
 * DjoSmer, 2024.02
 */

require 'scripts/pi-hole/php/header_authenticated.php';
?>
<div class="page-header">
    <h1>Update cache domains</h1>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    It works only with <a target="_blank" href="https://github.com/uklans/cache-domains">uklans/cache-domains</a>.
                    You can create your own fork (or branch) or get
                    <a target="_blank" href="https://github.com/DjoSmer/cache-domains">my fork</a>.
                </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="domain">Cache domains file url:</label>
                        <input id="cacheDomainsUrl" type="text" class="form-control"
                               placeholder="https://raw.githubusercontent.com/DjoSmer/cache-domains/master/cache_domains.json"
                               autocomplete="off" spellcheck="false" autocapitalize="none" autocorrect="off"/>
                    </div>
                </div>
            </div>
            <div class="box-footer clearfix">
                <button type="button" id="btnGetCacheDomains" class="btn btn-lg btn-primary btn-block">Get cache
                    domains
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box" id="recent-queries">
            <div class="box-header with-border">
                <h3 class="box-title">
                    List of group cache domains
                </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="ip">IP Address:</label>
                        <input id="lancacheIP" type="text" class="form-control" placeholder="Associated IP address"
                               autocomplete="off" spellcheck="false" autocapitalize="none" autocorrect="off"/>
                    </div>
                </div>
                <table id="listCacheDomains" class="table table-striped table-bordered" width="100%">
                    <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="status_allCheckboxes"/>
                        </th>
                        <th>Name</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="box-footer clearfix">
                <button type="button" id="btnUpdateCacheDomains" class="btn btn-lg btn-primary btn-block" disabled
                        data-toggle2="modal" data-target2="#outputModal">Update there
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="outputModal" role="dialog" data-keyboard="false" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Updating... this may take a while. <strong>Please do not navigate away from or
                        close this page.</strong>.</h4>
            </div>
            <div class="modal-body">
                <div class="box no-margin no-border no-shadow">
                    <p>You can view all domains in <a href="wildcarddns_records.php">Local DNS\Wildcard DNS Records</a>.</p>
                    <pre id="output" class="no-margin" style="width: 100%; height: 75vh; white-space: pre-wrap"></pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo fileversion('scripts/pi-hole/js/lancache/cacheDomains.js'); ?>"></script>

<?php
require 'scripts/pi-hole/php/footer.php';
?>
