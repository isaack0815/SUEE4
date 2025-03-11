<div id="metadata-management">
    <table class="table">
        <thead>
            <tr>
                <th>{translate key="key"}</th>
                <th>{translate key="value"}</th>
                <th>{translate key="description"}</th>
                <th>{translate key="actions"}</th>
            </tr>
        </thead>
        <tbody>
            {foreach $settings.metadata.values as $item}
                <tr>
                    <td>{$item.meta_key}</td>
                    <td>{$item.meta_value}</td>
                    <td>{$item.description}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary edit-metadata" data-id="{$item.id}" data-key="{$item.meta_key}" data-value="{$item.meta_value}" data-description="{$item.description}">{translate key="edit"}</button>
                        <button type="button" class="btn btn-sm btn-danger delete-metadata" data-id="{$item.id}">{translate key="delete"}</button>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>

    <button type="button" class="btn btn-success" id="add-metadata">{translate key="add_new_metadata"}</button>
</div>

<div class="modal fade" id="metadataModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="metadataModalLabel">{translate key="add_new_metadata"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="metadataForm">
                    <input type="hidden" id="metadata-id" name="id">
                    <input type="hidden" id="metadata-action" name="action" value="add">
                    <div class="mb-3">
                        <label for="metadata-key" class="form-label">{translate key="key"}</label>
                        <input type="text" class="form-control" id="metadata-key" name="key" required>
                    </div>
                    <div class="mb-3">
                        <label for="metadata-value" class="form-label">{translate key="value"}</label>
                        <input type="text" class="form-control" id="metadata-value" name="value" required>
                    </div>
                    <div class="mb-3">
                        <label for="metadata-description" class="form-label">{translate key="description"}</label>
                        <textarea class="form-control" id="metadata-description" name="description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-primary" id="saveMetadata">{translate key="save"}</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('metadataModal'));
    var addMetadataBtn = document.getElementById('add-metadata');
    var saveMetadataBtn = document.getElementById('saveMetadata');
    var metadataForm = document.getElementById('metadataForm');

    addMetadataBtn.addEventListener('click', function() {
        resetMetadataForm();
        document.getElementById('metadataModalLabel').textContent = '{translate key="add_new_metadata"}';
        modal.show();
    });

    document.querySelectorAll('.edit-metadata').forEach(function(button) {
        button.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var key = this.getAttribute('data-key');
            var value = this.getAttribute('data-value');
            var description = this.getAttribute('data-description');

            document.getElementById('metadata-id').value = id;
            document.getElementById('metadata-key').value = key;
            document.getElementById('metadata-value').value = value;
            document.getElementById('metadata-description').value = description;
            document.getElementById('metadata-action').value = 'edit';
            document.getElementById('metadataModalLabel').textContent = '{translate key="edit_metadata"}';
            modal.show();
        });
    });

    document.querySelectorAll('.delete-metadata').forEach(function(button) {
        button.addEventListener('click', function() {
            if (confirm('{translate key="confirm_delete"}')) {
                var id = this.getAttribute('data-id');
                var formData = new FormData();
                formData.append('id', id);
                formData.append('action', 'delete');
                sendMetadataRequest(formData);
            }
        });
    });

    saveMetadataBtn.addEventListener('click', function() {
        var formData = new FormData(metadataForm);
        sendMetadataRequest(formData);
    });

    function resetMetadataForm() {
        metadataForm.reset();
        document.getElementById('metadata-id').value = '';
        document.getElementById('metadata-action').value = 'add';
    }

    function sendMetadataRequest(formData) {
        fetch('api/metadata.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modal.hide();
                location.reload(); // Reload the page to show updated metadata
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
        });
    }
});
</script>

