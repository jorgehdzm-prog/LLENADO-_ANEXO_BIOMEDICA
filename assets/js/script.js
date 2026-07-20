// script.js - Funcionalidades JavaScript del sistema

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ============================================
    // 1. PREVISUALIZACIÓN DE IMÁGENES
    // ============================================
    function initImagePreviews() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                const previewId = 'preview-' + this.id;
                const preview = document.getElementById(previewId);
                
                if (!preview) return;
                
                preview.innerHTML = '';
                
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('img-thumbnail');
                        
                        preview.appendChild(img);
                        
                        // Botón para eliminar imagen
                        const removeBtn = document.createElement('button');
                        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                        removeBtn.className = 'btn-remove-image';
                        removeBtn.title = 'Eliminar imagen';
                        removeBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            preview.innerHTML = '';
                            input.value = '';
                        });
                        
                        preview.appendChild(removeBtn);
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    }

    // ============================================
    // 2. VALIDACIÓN DE FORMULARIOS
    // ============================================
    function initFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                let valid = true;
                const requiredFields = this.querySelectorAll('[required]');
                
                requiredFields.forEach(field => {
                    if (field.type === 'file') {
                        if (!field.files || field.files.length === 0) {
                            field.style.borderColor = 'red';
                            valid = false;
                            setTimeout(() => field.style.borderColor = '', 3000);
                        }
                    } else if (!field.value.trim()) {
                        field.style.borderColor = 'red';
                        valid = false;
                        setTimeout(() => field.style.borderColor = '', 3000);
                    }
                });
                
                if (!valid) {
                    e.preventDefault();
                    showNotification('Por favor, complete todos los campos requeridos.', 'error');
                    
                    // Scroll al primer error
                    const firstError = this.querySelector('[style*="border-color: red"]');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        });
    }

    // ============================================
    // 3. NOTIFICACIONES
    // ============================================
    function showNotification(message, type = 'success') {
        const colors = {
            success: { bg: '#d4edda', border: '#c3e6cb', color: '#155724' },
            error: { bg: '#f8d7da', border: '#f5c6cb', color: '#721c24' },
            warning: { bg: '#fff3cd', border: '#ffeaa7', color: '#856404' },
            info: { bg: '#d1ecf1', border: '#bee5eb', color: '#0c5460' }
        };
        
        const style = colors[type] || colors.info;
        
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            bottom: 25px;
            right: 25px;
            padding: 15px 25px;
            background: ${style.bg};
            border: 1px solid ${style.border};
            color: ${style.color};
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            z-index: 9999;
            max-width: 400px;
            font-size: 14px;
            font-family: 'Segoe UI', sans-serif;
            animation: slideIn 0.3s ease-out;
            display: flex;
            align-items: center;
            gap: 10px;
        `;
        
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        
        notification.innerHTML = `<span>${icons[type] || 'ℹ️'}</span> ${message}`;
        
        document.body.appendChild(notification);
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // ============================================
    // 4. MOSTRAR/OCULTAR CONTRASEÑA
    // ============================================
    function initPasswordToggle() {
        const toggleButtons = document.querySelectorAll('.toggle-password');
        
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input[type="password"], input[type="text"]');
                if (!input) return;
                
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            });
        });
    }

    // ============================================
    // 5. CONFIRMAR ELIMINACIÓN
    // ============================================
    function initDeleteConfirmation() {
        const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
        
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const message = this.getAttribute('data-confirm-message') || '¿Estás seguro de eliminar este registro?';
                
                if (confirm(message)) {
                    const url = this.getAttribute('data-url') || this.href;
                    if (url) {
                        window.location.href = url;
                    }
                }
            });
        });
    }

    // ============================================
    // 6. AUTO-OCULTAR ALERTAS
    // ============================================
    function initAutoHideAlerts() {
        const alerts = document.querySelectorAll('.alert-success, .alert-error, .alert-warning, .alert-info');
        
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
    }

    // ============================================
    // 7. ANIMACIONES CSS
    // ============================================
    function addAnimations() {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100px); opacity: 0; }
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-8px); }
                75% { transform: translateX(8px); }
            }
            .shake {
                animation: shake 0.4s ease-in-out;
            }
            .fade-in {
                animation: fadeIn 0.3s ease-out;
            }
        `;
        document.head.appendChild(style);
    }

    // ============================================
    // 8. TOOLTIP (información)
    // ============================================
    function initTooltips() {
        const tooltips = document.querySelectorAll('[data-tooltip]');
        
        tooltips.forEach(el => {
            el.addEventListener('mouseenter', function(e) {
                const tooltip = document.createElement('div');
                tooltip.textContent = this.getAttribute('data-tooltip');
                tooltip.style.cssText = `
                    position: fixed;
                    background: #2c3e50;
                    color: white;
                    padding: 6px 12px;
                    border-radius: 4px;
                    font-size: 12px;
                    z-index: 10000;
                    max-width: 250px;
                    pointer-events: none;
                    animation: fadeIn 0.2s ease-out;
                `;
                tooltip.id = 'custom-tooltip';
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.top = (rect.top - tooltip.offsetHeight - 8) + 'px';
                tooltip.style.left = (rect.left + rect.width/2 - tooltip.offsetWidth/2) + 'px';
            });
            
            el.addEventListener('mouseleave', function() {
                const tooltip = document.getElementById('custom-tooltip');
                if (tooltip) tooltip.remove();
            });
        });
    }

    // ============================================
    // 9. INICIALIZACIÓN
    // ============================================
    initImagePreviews();
    initFormValidation();
    initPasswordToggle();
    initDeleteConfirmation();
    initAutoHideAlerts();
    addAnimations();
    initTooltips();

    // Exponer notificaciones globalmente
    window.showNotification = showNotification;

    console.log('✅ Sistema cargado correctamente');
});