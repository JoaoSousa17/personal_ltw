/* Desenha o pop-up de Bloquio*/
function openBlockModal(userId, username) {
    document.getElementById('block_user_id').value = userId;
    document.getElementById('blockModalUsername').textContent = 'Usuário: ' + username;
    document.getElementById('blockModal').style.display = 'block';
}

/* Remove/Apaga o pop-up de Bloquio*/
function closeBlockModal() {
    document.getElementById('blockModal').style.display = 'none';
    document.getElementById('blockForm').reset();
}

/* Remove/Apaga o pop-up caso o utilizador clique fora deste */
window.onclick = function(event) {
    var modal = document.getElementById('blockModal');
    if (event.target == modal) {
        closeBlockModal();
    }
}