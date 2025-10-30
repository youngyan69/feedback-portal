function expandCard(card) {
    card.classList.toggle("expanded");
}

function replyFeedback(id) {
    window.location.href = `admin_reply.php?id=${id}`;
}

function editReply(id) {
    window.location.href = `admin_edit_reply.php?id=${id}`;
}
