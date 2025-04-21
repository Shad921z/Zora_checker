document.getElementById('zoraForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const wallet = document.getElementById('wallet').value;
  const resultDiv = document.getElementById('result');

  resultDiv.innerHTML = 'Checking...';

  const response = await fetch('check.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `wallet=${encodeURIComponent(wallet)}`
  });

  const data = await response.text();
  resultDiv.innerHTML = data;
});
