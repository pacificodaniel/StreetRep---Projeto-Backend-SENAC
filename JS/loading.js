/**
 * Controlar o loading spinner
 * Usar em requisições fetch, form submissions, etc.
 */

const LoadingSpinner = {
    /**
     * Mostrar o spinner
     * @param {string} message - Mensagem a exibir (padrão: "Carregando...")
     */
    show: function(message = 'Carregando...') {
        const spinner = document.getElementById('loadingSpinner');
        if (!spinner) return;
        
        const spinnerText = spinner.querySelector('.spinner-text');
        if (spinnerText) spinnerText.textContent = message;
        
        spinner.classList.add('show');
    },

    /**
     * Esconder o spinner
     */
    hide: function() {
        const spinner = document.getElementById('loadingSpinner');
        if (!spinner) return;
        
        spinner.classList.remove('show');
    },

    /**
     * Wrap de uma promessa (fetch, etc.)
     * @param {Promise} promise - Promise a envolver
     * @param {string} message - Mensagem a exibir
     * @returns {Promise}
     */
    wrap: function(promise, message = 'Carregando...') {
        this.show(message);
        return promise
            .finally(() => this.hide());
    }
};

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    // Garantir que o spinner existe
    if (!document.getElementById('loadingSpinner')) {
        const spinner = document.createElement('div');
        spinner.id = 'loadingSpinner';
        spinner.className = 'loading-spinner';
        spinner.innerHTML = `
            <div class="spinner-container">
                <div class="spinner"></div>
                <p class="spinner-text">Carregando...</p>
            </div>
        `;
        document.body.appendChild(spinner);
    }
});

/* LOADING SPINNER CSS */
const style = document.createElement('link');
style.rel = 'stylesheet';
style.href = '/STREETREP/css/loading.css';
document.head.appendChild(style);