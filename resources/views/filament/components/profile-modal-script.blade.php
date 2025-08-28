<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle click on profile menu items
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a[href="#"]');
        if (link) {
            const text = link.textContent.trim();
            if (text === 'Profile') {
                console.log('Profile link clicked, opening modal');
                e.preventDefault();
                e.stopPropagation();
                
                // Close dropdown
                const dropdown = link.closest('.fi-dropdown');
                if (dropdown) {
                    // Trigger close on dropdown
                    const button = document.querySelector('[aria-expanded="true"]');
                    if (button) {
                        button.click();
                    }
                }
                
                setTimeout(() => openProfileModal(), 100);
            }
        }
    });
    
    window.addEventListener('open-profile-modal', function(e) {
        console.log('Profile modal event received');
        openProfileModal();
    });
    
    function openProfileModal() {
        console.log('Opening profile modal...');
        
        // Remove any existing modal first
        const existingModal = document.getElementById('profile-modal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Create modal wrapper
        const modalWrapper = document.createElement('div');
        modalWrapper.id = 'profile-modal';
        modalWrapper.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 1rem;
        `;
        
        // Create modal content
        modalWrapper.innerHTML = `
            <div style="
                background: white;
                border-radius: 8px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                width: 100%;
                max-width: 28rem;
                max-height: 90vh;
                overflow-y: auto;
            ">
                <div style="padding: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <h2 style="font-size: 1.125rem; font-weight: 600; margin: 0; color: #1f2937;">Edit Profile</h2>
                        <button onclick="closeProfileModal()" style="
                            background: none;
                            border: none;
                            color: #9ca3af;
                            cursor: pointer;
                            padding: 0.25rem;
                        ">
                            <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form id="profile-form" onsubmit="submitProfileForm(event)" style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Avatar</label>
                            <input type="file" name="avatar_url" accept="image/*" style="
                                display: block;
                                width: 100%;
                                font-size: 0.875rem;
                                color: #6b7280;
                                border: 1px solid #d1d5db;
                                border-radius: 0.375rem;
                                padding: 0.5rem;
                            ">
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Name</label>
                            <input type="text" name="name" value="{{ auth()->user()->name ?? '' }}" required style="
                                width: 100%;
                                padding: 0.5rem 0.75rem;
                                border: 1px solid #d1d5db;
                                border-radius: 0.375rem;
                                font-size: 0.875rem;
                            ">
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Email</label>
                            <input type="email" name="email" value="{{ auth()->user()->email ?? '' }}" required style="
                                width: 100%;
                                padding: 0.5rem 0.75rem;
                                border: 1px solid #d1d5db;
                                border-radius: 0.375rem;
                                font-size: 0.875rem;
                            ">
                        </div>
                        
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">New Password (optional)</label>
                            <input type="password" name="password" style="
                                width: 100%;
                                padding: 0.5rem 0.75rem;
                                border: 1px solid #d1d5db;
                                border-radius: 0.375rem;
                                font-size: 0.875rem;
                            ">
                        </div>
                        
                        <div id="password-fields" style="display: none;">
                            <div style="margin-bottom: 1rem;">
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Confirm Password</label>
                                <input type="password" name="password_confirmation" style="
                                    width: 100%;
                                    padding: 0.5rem 0.75rem;
                                    border: 1px solid #d1d5db;
                                    border-radius: 0.375rem;
                                    font-size: 0.875rem;
                                ">
                            </div>
                            
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Current Password</label>
                                <input type="password" name="current_password" style="
                                    width: 100%;
                                    padding: 0.5rem 0.75rem;
                                    border: 1px solid #d1d5db;
                                    border-radius: 0.375rem;
                                    font-size: 0.875rem;
                                ">
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 0.75rem; padding-top: 1rem;">
                            <button type="submit" style="
                                flex: 1;
                                background-color: #2563eb;
                                color: white;
                                padding: 0.5rem 1rem;
                                border: none;
                                border-radius: 0.375rem;
                                cursor: pointer;
                                font-size: 0.875rem;
                                font-weight: 500;
                            ">
                                Update Profile
                            </button>
                            <button type="button" onclick="closeProfileModal()" style="
                                flex: 1;
                                background-color: #d1d5db;
                                color: #374151;
                                padding: 0.5rem 1rem;
                                border: none;
                                border-radius: 0.375rem;
                                cursor: pointer;
                                font-size: 0.875rem;
                                font-weight: 500;
                            ">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        
        // Add to body
        document.body.appendChild(modalWrapper);
        
        // Handle password field visibility
        const passwordInput = modalWrapper.querySelector('input[name="password"]');
        const passwordFields = modalWrapper.querySelector('#password-fields');
        
        passwordInput.addEventListener('input', function() {
            if (this.value) {
                passwordFields.style.display = 'block';
            } else {
                passwordFields.style.display = 'none';
            }
        });
        
        // Close on backdrop click
        modalWrapper.addEventListener('click', function(e) {
            if (e.target === modalWrapper) {
                closeProfileModal();
            }
        });
        
        // Close on escape key
        document.addEventListener('keydown', function escapeHandler(e) {
            if (e.key === 'Escape') {
                closeProfileModal();
                document.removeEventListener('keydown', escapeHandler);
            }
        });
        
        console.log('Profile modal created and displayed');
    }
    
    // Global functions for modal control
    window.closeProfileModal = function() {
        const modal = document.getElementById('profile-modal');
        if (modal) {
            modal.remove();
        }
    };
    
    window.submitProfileForm = async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        
        try {
            const response = await fetch('/admin/profile/update', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const result = await response.json();
                
                // Show success message
                alert('Profile updated successfully!');
                
                // Close modal
                closeProfileModal();
                
                // Reload page to show changes
                window.location.reload();
            } else {
                const error = await response.json();
                alert('Error: ' + (error.message || 'Update failed'));
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            alert('Error updating profile. Please try again.');
        }
    };
    
    console.log('Profile modal script initialized');
});
</script>