document.addEventListener("DOMContentLoaded", function () {
  const fileInput = document.getElementById("draftFile");
  const fileName  = document.getElementById("fileName");
  const uploadBtn = document.getElementById("uploadButton");
  const form = uploadBtn ? uploadBtn.closest("form") : null;

  if (!fileInput || !fileName || !uploadBtn || !form) return;

  uploadBtn.addEventListener("click", () => fileInput.click());

  fileInput.addEventListener("change", () => {
    fileName.textContent = fileInput.files?.[0]?.name || "Belum pilih file";
    if (fileInput.files?.length) form.requestSubmit(); // upload ke server
  });
});
