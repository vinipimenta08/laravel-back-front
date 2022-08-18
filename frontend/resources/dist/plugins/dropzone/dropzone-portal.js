  Dropzone.autoDiscover = false;
  noop = function() {};
  $("#frmFileUpload").dropzone({
      url: 'http://portal.voicce.com.br/portal/site/pages/post/post_cria_lista_preview.php',
      method: "post",
      withCredentials: false,
      parallelUploads: 2,
      uploadMultiple: false,
      maxFilesize: 256,
      paramName: "file",
      createImageThumbnails: true,
      maxThumbnailFilesize: 10,
      thumbnailWidth: 120,
      thumbnailHeight: 120,
      filesizeBase: 1000,
      maxFiles: 1,
      params: {},
      clickable: true,
      ignoreHiddenFiles: true,
      acceptedFiles: ".csv, .txt",
      acceptedMimeTypes: null,
      autoProcessQueue: true,
      autoQueue: true,
      addRemoveLinks: true,
      previewsContainer: null,
      hiddenInputContainer: "body",
      capture: null,
      renameFilename: null,
      dictDefaultMessage: "Soltar arquivos aqui para enviar",
      dictFallbackMessage: "Seu navegador não suporta uploads de arquivos arrastar e soltar.",
      dictFallbackText: "Por favor, use o formulário de fallback abaixo para enviar seus arquivos como nos velhos tempos.",
      dictFileTooBig: "O arquivo é muito grande no tamanho ({{filesize}}MiB). Tamanho máximo do arquivo: {{maxFilesize}}MiB.",
      dictInvalidFileType: "Você não pode fazer upload de arquivos desse tipo.",
      dictResponseError: "Servidor respondeu com código {{statusCode}}.",
      dictCancelUpload: "Cancelar upload",
      dictCancelUploadConfirmation: "Tem certeza de que deseja cancelar este upload?",
      dictRemoveFile: "Remover arquivo",
      dictRemoveFileConfirmation: null,
      dictMaxFilesExceeded: "Você não pode fazer upload de mais arquivos.",
      accept: function(file, done) {
          return done();
      },
      init: function() {
          return noop;
      },
      forceFallback: false,
      fallback: function() {
          var child, messageElement, span, _i, _len, _ref;
          this.element.className = "" + this.element.className + " dz-browser-not-supported";
          _ref = this.element.getElementsByTagName("div");
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          child = _ref[_i];
          if (/(^| )dz-message($| )/.test(child.className)) {
              messageElement = child;
              child.className = "dz-message";
              continue;
          }
          }
          if (!messageElement) {
          messageElement = Dropzone.createElement("<div class=\"dz-message\"><span></span></div>");
          this.element.appendChild(messageElement);
          }
          span = messageElement.getElementsByTagName("span")[0];
          if (span) {
          if (span.textContent != null) {
              span.textContent = this.options.dictFallbackMessage;
          } else if (span.innerText != null) {
              span.innerText = this.options.dictFallbackMessage;
          }
          }
          return this.element.appendChild(this.getFallbackForm());
      },
      resize: function(file) {
          var info, srcRatio, trgRatio;
          info = {
          srcX: 0,
          srcY: 0,
          srcWidth: file.width,
          srcHeight: file.height
          };
          srcRatio = file.width / file.height;
          info.optWidth = this.options.thumbnailWidth;
          info.optHeight = this.options.thumbnailHeight;
          if ((info.optWidth == null) && (info.optHeight == null)) {
          info.optWidth = info.srcWidth;
          info.optHeight = info.srcHeight;
          } else if (info.optWidth == null) {
          info.optWidth = srcRatio * info.optHeight;
          } else if (info.optHeight == null) {
          info.optHeight = (1 / srcRatio) * info.optWidth;
          }
          trgRatio = info.optWidth / info.optHeight;
          if (file.height < info.optHeight || file.width < info.optWidth) {
          info.trgHeight = info.srcHeight;
          info.trgWidth = info.srcWidth;
          } else {
          if (srcRatio > trgRatio) {
              info.srcHeight = file.height;
              info.srcWidth = info.srcHeight * trgRatio;
          } else {
              info.srcWidth = file.width;
              info.srcHeight = info.srcWidth / trgRatio;
          }
          }
          info.srcX = (file.width - info.srcWidth) / 2;
          info.srcY = (file.height - info.srcHeight) / 2;
          return info;
      },

      /*
      Those functions register themselves to the events on init and handle all
      the user interface specific stuff. Overwriting them won't break the upload
      but can break the way it's displayed.
      You can overwrite them if you don't like the default behavior. If you just
      want to add an additional event handler, register it on the dropzone object
      and don't overwrite those options.
      */
      drop: function(e) {
          return this.element.classList.remove("dz-drag-hover");
      },
      dragstart: noop,
      dragend: function(e) {
          return this.element.classList.remove("dz-drag-hover");
      },
      dragenter: function(e) {
          return this.element.classList.add("dz-drag-hover");
      },
      dragover: function(e) {
          return this.element.classList.add("dz-drag-hover");
      },
      dragleave: function(e) {
          return this.element.classList.remove("dz-drag-hover");
      },
      paste: noop,
      reset: function() {
          return this.element.classList.remove("dz-started");
      },
      addedfile: function(file) {
          var node, removeFileEvent, removeLink, _i, _j, _k, _len, _len1, _len2, _ref, _ref1, _ref2, _results;
          if (this.element === this.previewsContainer) {
          this.element.classList.add("dz-started");
          }
          if (this.previewsContainer) {
          file.previewElement = Dropzone.createElement(this.options.previewTemplate.trim());
          file.previewTemplate = file.previewElement;
          this.previewsContainer.appendChild(file.previewElement);
          _ref = file.previewElement.querySelectorAll("[data-dz-name]");
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
              node = _ref[_i];
              node.textContent = this._renameFilename(file.name);
          }
          _ref1 = file.previewElement.querySelectorAll("[data-dz-size]");
          for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
              node = _ref1[_j];
              node.innerHTML = this.filesize(file.size);
          }
          if (this.options.addRemoveLinks) {
              file._removeLink = Dropzone.createElement("<a class=\"dz-remove\" onclick=\"javascript:deletaArquivo('"+ file.name +"');\" data-dz-remove>" + this.options.dictRemoveFile + "</a>");
              file.previewElement.appendChild(file._removeLink);
          }
          removeFileEvent = (function(_this) {
              return function(e) {
              e.preventDefault();
              e.stopPropagation();
              if (file.status === Dropzone.UPLOADING) {
                  return Dropzone.confirm(_this.options.dictCancelUploadConfirmation, function() {
                  return _this.removeFile(file);
                  });
              } else {
                  if (_this.options.dictRemoveFileConfirmation) {
                  return Dropzone.confirm(_this.options.dictRemoveFileConfirmation, function() {
                      return _this.removeFile(file);
                  });
                  } else {
                  return _this.removeFile(file);
                  }
              }
              };
          })(this);
          _ref2 = file.previewElement.querySelectorAll("[data-dz-remove]");
          _results = [];
          for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
              removeLink = _ref2[_k];
              _results.push(removeLink.addEventListener("click", removeFileEvent));
          }
          return _results;
          }
      },
      removedfile: function(file) {
          var _ref;
          if (file.previewElement) {
          if ((_ref = file.previewElement) != null) {
              _ref.parentNode.removeChild(file.previewElement);
          }
          }
          return this._updateMaxFilesReachedClass();
      },
      thumbnail: function(file, dataUrl) {
          var thumbnailElement, _i, _len, _ref;
          if (file.previewElement) {
          file.previewElement.classList.remove("dz-file-preview");
          _ref = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
              thumbnailElement = _ref[_i];
              thumbnailElement.alt = file.name;
              thumbnailElement.src = dataUrl;
          }
          return setTimeout(((function(_this) {
              return function() {
              return file.previewElement.classList.add("dz-image-preview");
              };
          })(this)), 1);
          }
      },
      error: function(file, message) {
          var node, _i, _len, _ref, _results;
          if (file.previewElement) {
          file.previewElement.classList.add("dz-error");
          if (typeof message !== "String" && message.error) {
              message = message.error;
          }
          _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
              node = _ref[_i];
              _results.push(node.textContent = message);
          }
          return _results;
          }
      },
      errormultiple: noop,
      processing: function(file) {
          if (file.previewElement) {
          file.previewElement.classList.add("dz-processing");
          if (file._removeLink) {
              return file._removeLink.textContent = this.options.dictCancelUpload;
          }
          }
      },
      processingmultiple: noop,
      uploadprogress: function(file, progress, bytesSent) {
          var node, _i, _len, _ref, _results;
          if (file.previewElement) {
          _ref = file.previewElement.querySelectorAll("[data-dz-uploadprogress]");
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
              node = _ref[_i];
              if (node.nodeName === 'PROGRESS') {
              _results.push(node.value = progress);
              } else {
              _results.push(node.style.width = "" + progress + "%");
              }
          }
          return _results;
          }
      },
      totaluploadprogress: noop,
      sending: noop,
      sendingmultiple: noop,
      success: function(file) {
          if (file.previewElement) {
          verificaPreview(file.name);
          return file.previewElement.classList.add("dz-success");
          }
      },
      successmultiple: noop,
      canceled: function(file) {
          return this.emit("error", file, "Upload canceled.");
      },
      canceledmultiple: noop,
      complete: function(file) {
          if (file._removeLink) {
          file._removeLink.textContent = this.options.dictRemoveFile;
          }
          if (file.previewElement) {
          return file.previewElement.classList.add("dz-complete");
          }
      },
      completemultiple: noop,
      maxfilesexceeded: noop,
      maxfilesreached: noop,
      queuecomplete: noop,
      addedfiles: noop,
      previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n  <div class=\"dz-image\"><img data-dz-thumbnail /></div>\n  <div class=\"dz-details\">\n    <div class=\"dz-size\"><span data-dz-size></span></div>\n    <div class=\"dz-filename\"><span data-dz-name ></span></div>\n  </div>\n  <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n  <div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n  <div class=\"dz-success-mark\">\n    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:sketch=\"http://www.bohemiancoding.com/sketch/ns\">\n      <title>Check</title>\n      <defs></defs>\n      <g id=\"Page-1\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\" sketch:type=\"MSPage\">\n        <path d=\"M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\" id=\"Oval-2\" stroke-opacity=\"0.198794158\" stroke=\"#747474\" fill-opacity=\"0.816519475\" fill=\"#FFFFFF\" sketch:type=\"MSShapeGroup\"></path>\n      </g>\n    </svg>\n  </div>\n  <div class=\"dz-error-mark\">\n    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:sketch=\"http://www.bohemiancoding.com/sketch/ns\">\n      <title>Error</title>\n      <defs></defs>\n      <g id=\"Page-1\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\" sketch:type=\"MSPage\">\n        <g id=\"Check-+-Oval-2\" sketch:type=\"MSLayerGroup\" stroke=\"#747474\" stroke-opacity=\"0.198794158\" fill=\"#FFFFFF\" fill-opacity=\"0.816519475\">\n          <path d=\"M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\" id=\"Oval-2\" sketch:type=\"MSShapeGroup\"></path>\n        </g>\n      </g>\n    </svg>\n  </div>\n</div>"
  });