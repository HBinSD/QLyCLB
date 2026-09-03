<script>
  window.addEventListener('load', function () {
    const loader = document.getElementById('global-loader');
    if (loader) {
      loader.classList.add('hidden');
      // Xóa hẳn khỏi DOM sau khi chạy xong hiệu ứng chuyển mờ
      setTimeout(() => loader.remove(), 400);
    }
  });
</script>

</body>
</html>