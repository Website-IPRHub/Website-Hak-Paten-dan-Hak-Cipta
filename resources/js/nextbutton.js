document.addEventListener("DOMContentLoaded", () => {
  const nextLink = document.getElementById("nextLink");
  if (!nextLink) return;

  const scope = nextLink.closest("section") || document;

  // ambil form dan file input 
  const form = scope.querySelector("form#draftForm");
  const fileInput = scope.querySelector("#draftFile");
  const uploadButton = scope.querySelector("#uploadButton");
  const fileName = scope.querySelector("#fileName");

  // tombol upload + input hidden, klik tombol -> buka file picker
  if (uploadButton && fileInput) {
    uploadButton.addEventListener("click", (e) => {
      e.preventDefault();
      fileInput.click();
    });
  }

  // required fields (tetap)
  const requiredFields = scope.querySelectorAll(
    "input[required], select[required], textarea[required]"
  );

  // file required
  const requiredFile = scope.querySelector('input[type="file"][required]');

  const isFilled = (el) => {
    if (el.disabled) return true;
    if (el.offsetParent === null) return true;

    if (el.type === "checkbox" || el.type === "radio") return el.checked;
    return (el.value ?? "").trim() !== "";
  };

  const isFormReady = () => {
    for (const el of requiredFields) {
      if (el.type === "file") continue;
      if (!isFilled(el)) return false;
    }
    return true;
  };

  const isFileReady = () => {
    if (!requiredFile) return true;
    return requiredFile.files && requiredFile.files.length > 0;
  };

  const updateNextState = () => {
    const enabled = isFormReady() && isFileReady();
    nextLink.classList.toggle("is-disabled", !enabled);
  };

  updateNextState();

  requiredFields.forEach((el) => {
    el.addEventListener("input", updateNextState);
    el.addEventListener("change", updateNextState);
  });

  if (requiredFile) {
    requiredFile.addEventListener("change", () => {
      updateNextState();
      // tampilkan nama file
      if (fileName && requiredFile.files?.[0]?.name) {
        fileName.textContent = requiredFile.files[0].name;
      }
    });
  }

  // klik Next, submit form upload
  nextLink.addEventListener("click", (e) => {
    if (nextLink.classList.contains("is-disabled")) {
      e.preventDefault();
      return;
    }
    
    if (form) {
      e.preventDefault();
      form.submit();
    }
  });
});