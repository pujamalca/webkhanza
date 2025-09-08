@php
    $transferId = $getRecord()->id;
    $categoryId = $category_id;
    $task = \App\Models\BpjsTransferTask::where('bpjs_transfer_id', $transferId)
        ->where('category_id', $categoryId)
        ->with('completedBy')
        ->first();
    $isCompleted = $task?->is_completed ?? false;
    $completedBy = $task?->completedBy?->name ?? null;
    $completedAt = $task?->completed_at ?? null;
@endphp

<div class="flex items-center justify-center">
    <div class="relative group">
        <input 
            type="checkbox" 
            {{ $isCompleted ? 'checked' : '' }}
            class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            onchange="toggleBpjsTransferTask('{{ $transferId }}', '{{ $categoryId }}', this.checked, this)"
        />
        
        @if($isCompleted && $completedBy)
            <div class="absolute z-10 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-opacity bg-gray-900 text-white text-xs rounded p-2 -top-16 left-1/2 transform -translate-x-1/2 whitespace-nowrap">
                <div class="font-medium">✓ {{ $completedBy }}</div>
                @if($completedAt)
                    <div class="text-gray-300">{{ \Carbon\Carbon::parse($completedAt)->format('d/m/Y H:i') }}</div>
                @endif
                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
        @endif
    </div>
</div>

@once
<script>
function toggleBpjsTransferTask(transferId, categoryId, isCompleted, checkboxElement) {
    console.log('Toggling BPJS transfer task:', { 
        transferId: transferId, 
        categoryId: categoryId, 
        isCompleted: isCompleted
    });
    
    // Create form data untuk POST request
    const formData = new FormData();
    formData.append('transfer_id', transferId);
    formData.append('category_id', categoryId);
    formData.append('is_completed', isCompleted ? '1' : '0');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}');
    
    fetch('/admin/api/bpjs-transfer-task/toggle', {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status, response.statusText);
        return response.text().then(text => {
            console.log('Raw response text:', text);
            try {
                return JSON.parse(text);
            } catch(e) {
                console.error('Response not JSON:', text);
                throw new Error('Invalid JSON response: ' + text.substring(0, 200));
            }
        });
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Refresh halaman untuk update tooltip
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Terjadi kesalahan saat mengupdate status'));
            // Reset checkbox
            if (checkboxElement) {
                checkboxElement.checked = !isCompleted;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan: ' + error.message);
        // Reset checkbox
        if (checkboxElement) {
            checkboxElement.checked = !isCompleted;
        }
    });
}
</script>
@endonce