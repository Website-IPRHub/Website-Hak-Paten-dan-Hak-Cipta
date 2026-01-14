document.addEventListener("DOMContentLoaded", function () {
  const fileInput = document.getElementById("draftFile");
  const fileName = document.getElementById("fileName");

  if (!fileInput || !fileName) return;

  fileInput.addEventListener("change", function () {
    fileName.textContent = fileInput.files?.[0]?.name || "Belum Pilih File";
  });
});
