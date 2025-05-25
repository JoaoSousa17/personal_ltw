document.getElementById('differentAddresses').addEventListener('change', function() {
    const commonAddress = document.getElementById('commonAddress');
    const serviceDetails = document.querySelectorAll('.service-details');
    const specificAddresses = document.querySelectorAll('.specific-address');

    if (this.checked) {
      commonAddress.style.display = 'none';
      specificAddresses.forEach(address => {
        address.style.display = 'block';
      });
    } else {
      commonAddress.style.display = 'block';
      specificAddresses.forEach(address => {
        address.style.display = 'none';
      });
    }

    serviceDetails.forEach(service => {
      service.style.display = 'block';
    });
  });

  if (document.getElementById('differentAddresses').checked) {
    document.getElementById('commonAddress').style.display = 'none';
    document.querySelectorAll('.specific-address').forEach(address => {
      address.style.display = 'block';
    });
  } else {
    document.getElementById('commonAddress').style.display = 'block';
    document.querySelectorAll('.specific-address').forEach(address => {
      address.style.display = 'none';
    });
  }