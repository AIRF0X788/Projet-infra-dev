document.getElementById('cookie-accept').addEventListener('click', function() {
    document.getElementById('cookie-consent').classList.add('hidden');
  });
  
  document.getElementById('cookie-accept').addEventListener('click', function() {
    document.getElementById('cookie-consent').classList.add('hidden');
    document.cookie = "cookieAccepted=true; expires=Fri, 31 Dec 9999 23:59:59 GMT";
  });

if (document.cookie.includes("cookieAccepted=true")) {
  document.getElementById('cookie-consent').classList.add('hidden');
} else {
  document.getElementById('cookie-consent').classList.remove('hidden');
}