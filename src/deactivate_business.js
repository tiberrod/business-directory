// ==== deactivate_business.js ====

// Function to get the business ID from URL query string
function getBusinessIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id'); // returns the business ID as string
}

// Function to deactivate a business
async function deactivateBusiness(businessId, reason = '', onSuccessRedirect = null) {
    if (!businessId) {
        console.error('Business ID is required.');
        Swal.fire('Error', 'Business ID not found. Cannot deactivate.', 'error');
        return;
    }

    const url = `https://apploqic.my/api/v1/business`;

    try {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: businessId,
                reason: reason
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            console.log('Success:', data.message);
            Swal.fire({
                icon: 'success',
                title: 'Business Deactivated',
                text: `Reason: ${reason}`,
                confirmButtonText: 'OK'
            }).then(() => {
                if (onSuccessRedirect) {
                    window.location.href = onSuccessRedirect;
                }
            });
        } else {
            console.error('Error:', data.error.message);
            Swal.fire('Error', data.error.message, 'error');
        }
    } catch (err) {
        console.error('Request failed:', err);
        Swal.fire('Error', 'Something went wrong while deactivating the business.', 'error');
    }
}

// ==== Event listener for the deactivate button on view_details page ====
document.addEventListener('DOMContentLoaded', () => {
    const deactivateBtn = document.querySelector('#btn-deactivate');
    if (!deactivateBtn) return;

    deactivateBtn.addEventListener('click', () => {
        const businessId = getBusinessIdFromURL();

        Swal.fire({
            title: 'Deactivate Business',
            input: 'text',
            inputLabel: 'Enter reason for deactivation',
            inputPlaceholder: 'Type your reason here...',
            showCancelButton: true,
            confirmButtonText: 'Deactivate',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to provide a reason!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const reason = result.value;
                deactivateBusiness(businessId, reason, '../src/admin_index.php');
            }
        });
    });
});
