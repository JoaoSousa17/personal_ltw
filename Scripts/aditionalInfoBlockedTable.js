/**
 * Alterna a visibilidade das informações adicionais do motivo de bloqueio.
 * Altera também o símbolo de '+' para '-' conforme o estado.
 */
function toggleExtraInfo(element) {
    const infoElement = element.nextElementSibling;
    if (infoElement.style.display === 'none') {
        infoElement.style.display = 'block';
        element.textContent = '-';
    } else {
        infoElement.style.display = 'none';
        element.textContent = '+';
    }
}