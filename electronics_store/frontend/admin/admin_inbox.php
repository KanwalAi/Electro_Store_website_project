<?php
// -------------------------------------------------------------------------
// 1. PHP MAILER SETUP & DATABASE CONNECTION
// -------------------------------------------------------------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// !!! IMPORTANT: Adjust these paths to match where your PHPMailer folder is located !!!
require '../../backend/PHPMailer/Exception.php';
require '../../backend/PHPMailer/PHPMailer.php';
require '../../backend/PHPMailer/SMTP.php';

session_start();
include('../../backend/includes/db.php');

// Security Check
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { 
    header("location:../../backend/pages/login.php");
    exit;
}

$page_title = 'Admin Inbox';
include('../../backend/includes/header.php');

$statusMsg = ''; // To store success/error alerts

// -------------------------------------------------------------------------
// 2. LOGIC: SEND EMAIL (PHPMailer)
// -------------------------------------------------------------------------
if(isset($_POST['send_reply'])) {
    $message_id      = intval($_POST['message_id'] ?? 0);
    $recipient_email = $_POST['email'];
    $reply_content   = $_POST['reply_message'];
    
    $mail = new PHPMailer(true);

    try {
        // --- SMTP CONFIGURATION ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kanwal.ai.pk@gmail.com';        
        $mail->Password   = 'bcmivasryanskumn';           
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender & Recipient
        $mail->setFrom('kanwal.ai.pk@gmail.com', 'Admin Support');
        $mail->addAddress($recipient_email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Response to your inquiry';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; color: #333;'>
                <h3>Hello,</h3>
                <p>Thank you for contacting us. Here is the response to your inquiry:</p>
                <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;'>
                    " . nl2br(htmlspecialchars($reply_content)) . "
                </div>
                <p>Best regards,<br>Support Team</p>
            </div>
        ";

        $mail->send();
        
        // Mark original message as read immediately after replying
        if($message_id) {
            mysqli_query($conn, "UPDATE messages SET status='read' WHERE id=$message_id");
        }
        
        $statusMsg = '<div class="alert alert-success my-3">Reply sent successfully to ' . htmlspecialchars($recipient_email) . '!</div>';
    } catch (Exception $e) {
        $statusMsg = '<div class="alert alert-danger my-3">Message could not be sent. Mailer Error: ' . htmlspecialchars($mail->ErrorInfo) . '</div>';
    }
}

// -------------------------------------------------------------------------
// 3. LOGIC: DATABASE ACTIONS (Read/Delete)
// -------------------------------------------------------------------------

// Mark as read
if(isset($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);
    mysqli_query($conn, "UPDATE messages SET status='read' WHERE id=$id");
    echo "<script>window.location.href='admin_inbox.php';</script>"; 
}

// Delete message
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM messages WHERE id=$id");
    echo "<script>window.location.href='admin_inbox.php';</script>"; 
}

// -------------------------------------------------------------------------
// FIXED QUERY: Sort by Condition (Unread first)
// -------------------------------------------------------------------------
// This logic: (status='unread') returns 1 (True) or 0 (False). 
// Sorting DESC puts the 1s (Unread) at the top.
$messages = mysqli_query($conn, "SELECT * FROM messages ORDER BY (status='unread') DESC, created_at DESC");
$temp_messages = mysqli_fetch_all($messages, MYSQLI_ASSOC);

// Calculate Unread Count
$unread_count = 0;
foreach($temp_messages as $msg) {
    if($msg['status'] == 'unread') $unread_count++;
}
?>

<div class="container my-5">
    <div class="section-header mb-4 d-flex justify-content-between align-items-center">
        <h2><i class="fas fa-envelope"></i> Customer Messages</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Admin Dashboard</a>
    </div>

    <?php echo $statusMsg; ?>

    <?php if($unread_count > 0): ?>
    <div class="alert alert-info shadow-sm">
        <i class="fas fa-bell"></i> You have <strong><?php echo $unread_count; ?></strong> unread messages
    </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach($temp_messages as $msg): ?>
        <div class="col-lg-10 mx-auto mb-5">
            <div class="card shadow-lg border-0 <?php echo $msg['status'] == 'unread' ? 'border-primary' : ''; ?>">

                <div class="card-header <?php echo $msg['status'] == 'unread' ? 'bg-info text-white' : 'bg-light'; ?>">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="fas fa-user-circle"></i>
                                <?php echo htmlspecialchars($msg['user_name']); ?></h5>
                            <small style="opacity: 0.9"><?php echo htmlspecialchars($msg['user_email']); ?></small>
                        </div>
                        <?php if($msg['status'] == 'unread'): ?>
                        <span class="badge bg-danger rounded-pill">New</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">
                    <?php $preview = mb_strlen($msg['message']) > 120 ? mb_substr($msg['message'],0,120) . '...' : $msg['message']; ?>
                    <p class="mb-2"><strong>Message Preview:</strong> <?php echo nl2br(htmlspecialchars($preview)); ?>
                    </p>
                    <small class="text-muted d-block mb-2">
                        <i class="fas fa-clock"></i> <?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?>
                    </small>

                    <a class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse"
                        href="#collapse_<?php echo $msg['id']; ?>" role="button" aria-expanded="false"
                        aria-controls="collapse_<?php echo $msg['id']; ?>">
                        <i class="fas fa-eye"></i> View & Reply
                    </a>

                    <div class="collapse mt-3" id="collapse_<?php echo $msg['id']; ?>">
                        <div class="card card-body">
                            <h6 class="mb-2">Full Message</h6>
                            <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>

                            <hr>

                            <h6 class="text-primary"><i class="fas fa-reply"></i> Send a Reply</h6>
                            <form method="post" action="">
                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                <input type="hidden" name="email"
                                    value="<?php echo htmlspecialchars($msg['user_email']); ?>">

                                <div class="mb-2">
                                    <textarea name="reply_message" class="form-control" rows="4"
                                        placeholder="Type your response here..." required></textarea>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" name="send_reply" class="btn btn-success btn-sm">
                                        <i class="fas fa-paper-plane"></i> Send Email
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white d-flex justify-content-between">
                    <?php if($msg['status'] == 'unread'): ?>
                    <a href="admin_inbox.php?mark_read=<?php echo $msg['id']; ?>"
                        class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-check"></i> Mark as Read
                    </a>
                    <?php else: ?>
                    <button class="btn btn-sm btn-secondary" disabled>Read</button>
                    <?php endif; ?>

                    <a href="admin_inbox.php?delete=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('Are you sure you want to delete this message?')">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </div>

            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include('../../backend/includes/footer.php'); ?>