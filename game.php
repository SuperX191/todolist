<?php
include 'nav.php';
session_start();
include 'db.php';

$pin = $_GET['pin'];
$stmt = $pdo->prepare("SELECT id FROM rooms WHERE pin_code = ?");
$stmt->execute([$pin]);
$room = $stmt->fetch();
if (!$room) { echo "Room not found"; exit; }

$room_id = $room['id'];
$user_id = $_SESSION['user_id'];

// Get current user info
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch();

// Get all users in the room
$stmt = $pdo->prepare("
    SELECT DISTINCT u.id, u.username 
    FROM chat_messages cm 
    JOIN users u ON cm.user_id = u.id 
    WHERE cm.room_id = ? 
    ORDER BY u.username
");
$stmt->execute([$room_id]);
$room_users = $stmt->fetchAll();

// Create game table if not exists
$pdo->exec("
    CREATE TABLE IF NOT EXISTS werewolf_games (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_id INT,
        game_state ENUM('lobby', 'writing', 'voting', 'results', 'ended') DEFAULT 'lobby',
        round_number INT DEFAULT 1,
        topics JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (room_id) REFERENCES rooms(id)
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS werewolf_players (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT,
        user_id INT,
        role ENUM('villager', 'werewolf'),
        is_alive BOOLEAN DEFAULT TRUE,
        is_ready BOOLEAN DEFAULT FALSE,
        writing_content TEXT,
        FOREIGN KEY (game_id) REFERENCES werewolf_games(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS werewolf_votes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT,
        voter_id INT,
        voted_for_id INT,
        round_number INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (game_id) REFERENCES werewolf_games(id),
        FOREIGN KEY (voter_id) REFERENCES users(id),
        FOREIGN KEY (voted_for_id) REFERENCES users(id)
    )
");

// Handle new game parameter
if (isset($_GET['new_game'])) {
    // End current game if exists
    $stmt = $pdo->prepare("SELECT id FROM werewolf_games WHERE room_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$room_id]);
    $old_game = $stmt->fetch();
    if ($old_game) {
        $stmt = $pdo->prepare("UPDATE werewolf_games SET game_state = 'ended' WHERE id = ?");
        $stmt->execute([$old_game['id']]);
    }
    header("Location: werewolf_game.php?pin=" . $pin);
    exit;
}

// Get current game
$stmt = $pdo->prepare("SELECT * FROM werewolf_games WHERE room_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$room_id]);
$current_game = $stmt->fetch();

// Handle form submissions
if ($_POST['action'] ?? '' === 'join_game') {
    if (!$current_game || $current_game['game_state'] === 'ended') {
        // Create new game
        $stmt = $pdo->prepare("INSERT INTO werewolf_games (room_id) VALUES (?)");
        $stmt->execute([$room_id]);
        $game_id = $pdo->lastInsertId();
    } else {
        $game_id = $current_game['id'];
    }
    
    // Add player to game
    $stmt = $pdo->prepare("INSERT IGNORE INTO werewolf_players (game_id, user_id, is_ready) VALUES (?, ?, TRUE) ON DUPLICATE KEY UPDATE is_ready = TRUE");
    $stmt->execute([$game_id, $user_id]);
    
    header("Location: werewolf_game.php?pin=" . $pin);
    exit;
}

if ($_POST['action'] ?? '' === 'start_game') {
    if ($current_game) {
        $game_id = $current_game['id'];
        
        // Get all ready players
        $stmt = $pdo->prepare("SELECT user_id FROM werewolf_players WHERE game_id = ? AND is_ready = TRUE");
        $stmt->execute([$game_id]);
        $ready_players = $stmt->fetchAll();
        
        if (count($ready_players) >= 3) {
            // Assign roles (1 werewolf for every 3-4 players)
            $werewolf_count = max(1, floor(count($ready_players) / 3));
            $werewolves = array_rand($ready_players, $werewolf_count);
            if (!is_array($werewolves)) $werewolves = [$werewolves];
            
            foreach ($ready_players as $index => $player) {
                $role = in_array($index, $werewolves) ? 'werewolf' : 'villager';
                $stmt = $pdo->prepare("UPDATE werewolf_players SET role = ? WHERE game_id = ? AND user_id = ?");
                $stmt->execute([$role, $game_id, $player['user_id']]);
            }
            
            // Generate topics
            $villager_topics = [
                "อธิบายวิธีทำไข่เจียว",
                "เล่าเรื่องการเดินทางไปทะเล",
                "อธิบายวิธีการดูแลสุนัข",
                "เล่าเรื่องวันหยุดที่ประทับใจ",
                "อธิบายวิธีทำข้าวผัด"
            ];
            
            $werewolf_topics = [
                "อธิบายวิธีทำไข่ต้ม", // ใกล้เคียงกับไข่เจียว
                "เล่าเรื่องการเดินทางไปภูเขา", // ใกล้เคียงกับทะเล
                "อธิบายวิธีการดูแลแมว", // ใกล้เคียงกับสุนัข
                "เล่าเรื่องวันทำงานที่ประทับใจ", // ใกล้เคียงกับวันหยุด
                "อธิบายวิธีทำข้าวต้ม" // ใกล้เคียงกับข้าวผัด
            ];
            
            $topic_index = array_rand($villager_topics);
            $topics = [
                'villager' => $villager_topics[$topic_index],
                'werewolf' => $werewolf_topics[$topic_index]
            ];
            
            // Update game state
            $stmt = $pdo->prepare("UPDATE werewolf_games SET game_state = 'writing', topics = ? WHERE id = ?");
            $stmt->execute([json_encode($topics), $game_id]);
        }
    }
    
    header("Location: werewolf_game.php?pin=" . $pin);
    exit;
}

if ($_POST['action'] ?? '' === 'submit_writing') {
    if ($current_game && $_POST['writing_content']) {
        $stmt = $pdo->prepare("UPDATE werewolf_players SET writing_content = ? WHERE game_id = ? AND user_id = ?");
        $stmt->execute([$_POST['writing_content'], $current_game['id'], $user_id]);
        
        // Check if all players have submitted
        $stmt = $pdo->prepare("SELECT COUNT(*) as total, COUNT(writing_content) as submitted FROM werewolf_players WHERE game_id = ? AND is_alive = TRUE");
        $stmt->execute([$current_game['id']]);
        $counts = $stmt->fetch();
        
        if ($counts['total'] == $counts['submitted']) {
            // Move to voting phase
            $stmt = $pdo->prepare("UPDATE werewolf_games SET game_state = 'voting' WHERE id = ?");
            $stmt->execute([$current_game['id']]);
        }
    }
    
    header("Location: werewolf_game.php?pin=" . $pin);
    exit;
}

if ($_POST['action'] ?? '' === 'vote') {
    if ($current_game && $_POST['voted_for']) {
        // Delete previous vote
        $stmt = $pdo->prepare("DELETE FROM werewolf_votes WHERE game_id = ? AND voter_id = ? AND round_number = ?");
        $stmt->execute([$current_game['id'], $user_id, $current_game['round_number']]);
        
        // Insert new vote
        $stmt = $pdo->prepare("INSERT INTO werewolf_votes (game_id, voter_id, voted_for_id, round_number) VALUES (?, ?, ?, ?)");
        $stmt->execute([$current_game['id'], $user_id, $_POST['voted_for'], $current_game['round_number']]);
        
        // Check if all players have voted
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT voter_id) as voted, COUNT(DISTINCT wp.user_id) as total FROM werewolf_votes wv RIGHT JOIN werewolf_players wp ON wp.user_id = wv.voter_id WHERE wp.game_id = ? AND wp.is_alive = TRUE AND (wv.round_number = ? OR wv.round_number IS NULL)");
        $stmt->execute([$current_game['id'], $current_game['round_number']]);
        $vote_counts = $stmt->fetch();
        
        if ($vote_counts['voted'] == $vote_counts['total']) {
            // Process voting results
            $stmt = $pdo->prepare("
                SELECT voted_for_id, u.username, COUNT(*) as vote_count 
                FROM werewolf_votes wv 
                JOIN users u ON wv.voted_for_id = u.id 
                WHERE game_id = ? AND round_number = ? 
                GROUP BY voted_for_id 
                ORDER BY vote_count DESC 
                LIMIT 1
            ");
            $stmt->execute([$current_game['id'], $current_game['round_number']]);
            $eliminated = $stmt->fetch();
            
            if ($eliminated) {
                // Eliminate player
                $stmt = $pdo->prepare("UPDATE werewolf_players SET is_alive = FALSE WHERE game_id = ? AND user_id = ?");
                $stmt->execute([$current_game['id'], $eliminated['voted_for_id']]);
                
                // Check win conditions
                $stmt = $pdo->prepare("SELECT role, COUNT(*) as count FROM werewolf_players WHERE game_id = ? AND is_alive = TRUE GROUP BY role");
                $stmt->execute([$current_game['id']]);
                $role_counts = [];
                while ($row = $stmt->fetch()) {
                    $role_counts[$row['role']] = $row['count'];
                }
                
                $werewolves = $role_counts['werewolf'] ?? 0;
                $villagers = $role_counts['villager'] ?? 0;
                
                if ($werewolves == 0) {
                    // Villagers win
                    $stmt = $pdo->prepare("UPDATE werewolf_games SET game_state = 'ended' WHERE id = ?");
                    $stmt->execute([$current_game['id']]);
                } elseif ($werewolves >= $villagers) {
                    // Werewolves win
                    $stmt = $pdo->prepare("UPDATE werewolf_games SET game_state = 'ended' WHERE id = ?");
                    $stmt->execute([$current_game['id']]);
                } else {
                    // Continue game - new round
                    $stmt = $pdo->prepare("UPDATE werewolf_games SET game_state = 'writing', round_number = round_number + 1 WHERE id = ?");
                    $stmt->execute([$current_game['id']]);
                    
                    // Clear writing content for next round
                    $stmt = $pdo->prepare("UPDATE werewolf_players SET writing_content = NULL WHERE game_id = ?");
                    $stmt->execute([$current_game['id']]);
                }
                
                // Set results state temporarily
                $stmt = $pdo->prepare("UPDATE werewolf_games SET game_state = 'results' WHERE id = ?");
                $stmt->execute([$current_game['id']]);
            }
        }
    }
    
    header("Location: werewolf_game.php?pin=" . $pin);
    exit;
}

// Refresh current game data
$stmt = $pdo->prepare("SELECT * FROM werewolf_games WHERE room_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$room_id]);
$current_game = $stmt->fetch();

// Get game players
$game_players = [];
$ready_players = [];
$my_role = null;
$topics = [];

if ($current_game) {
    $stmt = $pdo->prepare("
        SELECT wp.*, u.username 
        FROM werewolf_players wp 
        JOIN users u ON wp.user_id = u.id 
        WHERE wp.game_id = ? 
        ORDER BY u.username
    ");
    $stmt->execute([$current_game['id']]);
    $game_players = $stmt->fetchAll();
    
    $ready_players = array_filter($game_players, function($p) { return $p['is_ready']; });
    
    foreach ($game_players as $player) {
        if ($player['user_id'] == $user_id) {
            $my_role = $player['role'];
            break;
        }
    }
    
    if ($current_game['topics']) {
        $topics = json_decode($current_game['topics'], true);
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Werewolf Writing Game - Room <?= htmlspecialchars($pin) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Creepster&family=Nosifer&display=swap" rel="stylesheet">
    <style>
        :root {
            --dark-bg: #1a1a2e;
            --darker-bg: #16213e;
            --accent: #e94560;
            --gold: #f39c12;
            --text-light: #eee;
            --shadow: rgba(0,0,0,0.5);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, var(--dark-bg), var(--darker-bg));
            min-height: 100vh;
            color: var(--text-light);
            font-family: 'Arial', sans-serif;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(233, 69, 96, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(243, 156, 18, 0.1) 0%, transparent 50%);
        }
        
        .game-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            margin-top: 80px;
        }
        
        .game-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .game-title {
            font-family: 'Creepster', cursive;
            font-size: 3rem;
            color: var(--accent);
            text-shadow: 3px 3px 0 var(--shadow);
            margin-bottom: 10px;
        }
        
        .room-info {
            background: var(--darker-bg);
            padding: 15px;
            border-radius: 10px;
            border: 2px solid var(--accent);
            display: inline-block;
        }
        
        .game-section {
            background: var(--darker-bg);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(233, 69, 96, 0.3);
            box-shadow: 0 10px 30px var(--shadow);
        }
        
        .section-title {
            font-size: 1.5rem;
            color: var(--gold);
            margin-bottom: 15px;
            text-align: center;
        }
        
        .players-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .player-card {
            background: var(--dark-bg);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .player-card.ready {
            border-color: var(--gold);
            box-shadow: 0 0 15px rgba(243, 156, 18, 0.3);
        }
        
        .player-card.eliminated {
            opacity: 0.5;
            border-color: #666;
        }
        
        .player-card.werewolf {
            border-color: var(--accent);
            background: linear-gradient(135deg, var(--dark-bg), rgba(233, 69, 96, 0.1));
        }
        
        .role-indicator {
            font-size: 0.8rem;
            margin-top: 5px;
            font-weight: bold;
        }
        
        .role-werewolf {
            color: var(--accent);
        }
        
        .role-villager {
            color: var(--gold);
        }
        
        .btn {
            background: var(--accent);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        
        .btn:hover {
            background: #d63447;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px var(--shadow);
        }
        
        .btn-gold {
            background: var(--gold);
        }
        
        .btn-gold:hover {
            background: #e67e22;
        }
        
        .writing-area {
            margin: 20px 0;
        }
        
        .topic-card {
            background: var(--dark-bg);
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid var(--gold);
            margin-bottom: 15px;
        }
        
        .topic-text {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--gold);
        }
        
        .writing-input {
            width: 100%;
            min-height: 150px;
            background: var(--dark-bg);
            color: var(--text-light);
            border: 2px solid rgba(233, 69, 96, 0.3);
            border-radius: 10px;
            padding: 15px;
            font-size: 1rem;
            resize: vertical;
        }
        
        .writing-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 15px rgba(233, 69, 96, 0.3);
        }
        
        .writings-display {
            display: grid;
            gap: 15px;
        }
        
        .writing-card {
            background: var(--dark-bg);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid rgba(233, 69, 96, 0.3);
        }
        
        .writing-author {
            font-weight: bold;
            color: var(--gold);
            margin-bottom: 10px;
        }
        
        .writing-content {
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .vote-section {
            text-align: center;
        }
        
        .game-status {
            text-align: center;
            padding: 20px;
            background: var(--dark-bg);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .status-text {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        
        .round-indicator {
            color: var(--gold);
            font-size: 1.1rem;
        }
        
        .results-section {
            text-align: center;
            padding: 30px;
        }
        
        .eliminated-player {
            font-size: 1.5rem;
            color: var(--accent);
            margin: 20px 0;
        }
        
        @media (max-width: 768px) {
            .game-title {
                font-size: 2rem;
            }
            
            .players-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="game-container">
        <div class="game-header">
            <h1 class="game-title">🐺 Werewolf Writing 🐺</h1>
            <div class="room-info">
                <strong>Room PIN: <?= htmlspecialchars($pin) ?></strong><br>
                Player: <?= htmlspecialchars($current_user['username']) ?>
                <?php if ($my_role): ?>
                    <br><span class="role-<?= $my_role ?> role-indicator">
                        <?= $my_role === 'werewolf' ? '🐺 WEREWOLF' : '👥 VILLAGER' ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$current_game || $current_game['game_state'] === 'lobby'): ?>
            <!-- Lobby Phase -->
            <div class="game-section">
                <h2 class="section-title">🎮 Game Lobby</h2>
                
                <div class="players-grid">
                    <?php foreach ($room_users as $user): ?>
                        <div class="player-card <?= in_array($user['id'], array_column($ready_players, 'user_id')) ? 'ready' : '' ?>">
                            <div><?= htmlspecialchars($user['username']) ?></div>
                            <?php if (in_array($user['id'], array_column($ready_players, 'user_id'))): ?>
                                <div style="color: var(--gold);">✓ Ready</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="text-align: center;">
                    <?php if (!in_array($user_id, array_column($ready_players, 'user_id'))): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="join_game">
                            <button type="submit" class="btn btn-gold">🎮 Join Game</button>
                        </form>
                    <?php else: ?>
                        <p style="color: var(--gold); margin-bottom: 15px;">✓ You're ready to play!</p>
                        <?php if (count($ready_players) >= 3): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="start_game">
                                <button type="submit" class="btn">🚀 Start Game</button>
                            </form>
                        <?php else: ?>
                            <p style="color: var(--accent);">Need at least 3 players to start</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($current_game['game_state'] === 'writing'): ?>
            <!-- Writing Phase -->
            <div class="game-section">
                <div class="game-status">
                    <div class="status-text">✍️ Writing Phase</div>
                    <div class="round-indicator">Round <?= $current_game['round_number'] ?></div>
                </div>
                
                <?php if ($my_role && $topics): ?>
                    <div class="topic-card">
                        <div class="topic-text">
                            📝 Your Topic: <?= htmlspecialchars($topics[$my_role]) ?>
                        </div>
                    </div>
                    
                    <?php 
                    $my_player = array_filter($game_players, function($p) use ($user_id) { 
                        return $p['user_id'] == $user_id; 
                    });
                    $my_player = reset($my_player);
                    ?>
                    
                    <?php if (!$my_player['writing_content']): ?>
                        <form method="POST" class="writing-area">
                            <input type="hidden" name="action" value="submit_writing">
                            <textarea name="writing_content" class="writing-input" placeholder="Write your response here..." required></textarea>
                            <div style="text-align: center; margin-top: 15px;">
                                <button type="submit" class="btn">📝 Submit Writing</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="game-status">
                            <p style="color: var(--gold);">✅ Your writing has been submitted!</p>
                            <p>Waiting for other players...</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        <?php elseif ($current_game['game_state'] === 'voting'): ?>
            <!-- Voting Phase -->
            <div class="game-section">
                <div class="game-status">
                    <div class="status-text">🗳️ Voting Phase</div>
                    <div class="round-indicator">Round <?= $current_game['round_number'] ?></div>
                    <p>Vote for the most suspicious writing!</p>
                </div>
                
                <div class="writings-display">
                    <?php foreach ($game_players as $player): ?>
                        <?php if ($player['is_alive'] && $player['writing_content']): ?>
                            <div class="writing-card">
                                <div class="writing-author">👤 <?= htmlspecialchars($player['username']) ?></div>
                                <div class="writing-content"><?= nl2br(htmlspecialchars($player['writing_content'])) ?></div>
                                
                                <?php if ($player['user_id'] != $user_id): ?>
                                    <div class="vote-section">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="vote">
                                            <input type="hidden" name="voted_for" value="<?= $player['user_id'] ?>">
                                            <button type="submit" class="btn">🗳️ Vote This Player</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif ($current_game['game_state'] === 'results'): ?>
            <!-- Results Phase -->
            <div class="game-section">
                <div class="results-section">
                    <h2 class="section-title">📊 Round Results</h2>
                    
                    <?php
                    // Get elimination results
                    $stmt = $pdo->prepare("
                        SELECT u.username, wp.role, COUNT(wv.id) as votes
                        FROM werewolf_votes wv 
                        JOIN users u ON wv.voted_for_id = u.id 
                        JOIN werewolf_players wp ON wp.user_id = u.id AND wp.game_id = ?
                        WHERE wv.game_id = ? AND wv.round_number = ? 
                        GROUP BY wv.voted_for_id 
                        ORDER BY votes DESC 
                        LIMIT 1
                    ");
                    $stmt->execute([$current_game['id'], $current_game['id'], $current_game['round_number']]);
                    $eliminated = $stmt->fetch();
                    ?>
                    
                    <?php if ($eliminated): ?>
                        <div class="eliminated-player">
                            ⚰️ <?= htmlspecialchars($eliminated['username']) ?> was eliminated!<br>
                            <span style="font-size: 1rem;">
                                They were a <?= $eliminated['role'] === 'werewolf' ? '🐺 WEREWOLF' : '👥 VILLAGER' ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <script>
                        setTimeout(function() {
                            window.location.reload();
                        }, 3000);
                    </script>
                </div>
            </div>

        <?php elseif ($current_game['game_state'] === 'ended'): ?>
            <!-- Game Over -->
            <div class="game-section">
                <div class="results-section">
                    <h2 class="section-title">🎉 Game Over!</h2>
                    
                    <?php
                    // Check who won
                    $stmt = $pdo->prepare("SELECT role, COUNT(*) as count FROM werewolf_players WHERE game_id = ? AND is_alive = TRUE GROUP BY role");
                    $stmt->execute([$current_game['id']]);
                    $survivors = [];
                    while ($row = $stmt->fetch()) {
                        $survivors[$row['role']] = $row['count'];
                    }
                    
                    $werewolves_alive = $survivors['werewolf'] ?? 0;
                    $villagers_alive = $survivors['villager'] ?? 0;
                    ?>
                    
                    <?php if ($werewolves_alive == 0): ?>
                        <div style="font-size: 2rem; color: var(--gold); margin-bottom: 20px;">
                            👥 VILLAGERS WIN! 🎉
                        </div>
                        <p>All werewolves have been eliminated!</p>
                    <?php else: ?>
                        <div style="font-size: 2rem; color: var(--accent); margin-bottom: 20px;">
                            🐺 WEREWOLVES WIN! 🎉
                        </div>
                        <p>The werewolves have taken over the village!</p>
                    <?php endif; ?>
                    
                    <!-- Show all players and their roles -->
                    <div style="margin-top: 30px;">
                        <h3 style="color: var(--gold); margin-bottom: 15px;">Player Roles:</h3>
                        <div class="players-grid">
                            <?php foreach ($game_players as $player): ?>
                                <div class="player-card <?= $player['role'] ?> <?= !$player['is_alive'] ? 'eliminated' : '' ?>">
                                    <div><?= htmlspecialchars($player['username']) ?></div>
                                    <div class="role-indicator role-<?= $player['role'] ?>">
                                        <?= $player['role'] === 'werewolf' ? '🐺 WEREWOLF' : '👥 VILLAGER' ?>
                                    </div>
                                    <?php if (!$player['is_alive']): ?>
                                        <div style="color: #666; font-size: 0.8rem;">⚰️ Eliminated</div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <a href="werewolf_game.php?pin=<?= $pin ?>&new_game=1" class="btn btn-gold">🎮 Play Again</a>
                        <a href="board.php?pin=<?= $pin ?>" class="btn">🔙 Back to Chat</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Players Status Panel -->
        <?php if ($current_game && $current_game['game_state'] !== 'lobby' && $current_game['game_state'] !== 'ended'): ?>
            <div class="game-section">
                <h3 style="color: var(--gold); margin-bottom: 15px;">👥 Players Status</h3>
                <div class="players-grid">
                    <?php foreach ($game_players as $player): ?>
                        <div class="player-card <?= !$player['is_alive'] ? 'eliminated' : '' ?>">
                            <div><?= htmlspecialchars($player['username']) ?></div>
                            <?php if (!$player['is_alive']): ?>
                                <div style="color: #666; font-size: 0.8rem;">⚰️ Eliminated</div>
                            <?php elseif ($current_game['game_state'] === 'writing' && $player['writing_content']): ?>
                                <div style="color: var(--gold); font-size: 0.8rem;">✅ Written</div>
                            <?php elseif ($current_game['game_state'] === 'writing'): ?>
                                <div style="color: var(--accent); font-size: 0.8rem;">✍️ Writing...</div>
                            <?php elseif ($current_game['game_state'] === 'voting'): ?>
                                <?php
                                // Check if this player has voted
                                $stmt = $pdo->prepare("SELECT COUNT(*) as voted FROM werewolf_votes WHERE game_id = ? AND voter_id = ? AND round_number = ?");
                                $stmt->execute([$current_game['id'], $player['user_id'], $current_game['round_number']]);
                                $has_voted = $stmt->fetch()['voted'] > 0;
                                ?>
                                <?php if ($has_voted): ?>
                                    <div style="color: var(--gold); font-size: 0.8rem;">✅ Voted</div>
                                <?php else: ?>
                                    <div style="color: var(--accent); font-size: 0.8rem;">🗳️ Voting...</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Back to chat button -->
        <div style="text-align: center; margin-top: 20px;">
            <a href="board.php?pin=<?= $pin ?>" class="btn">💬 Back to Chat Room</a>
        </div>
    </div>
    
    <script>
        // Auto refresh for real-time updates
        <?php if ($current_game && $current_game['game_state'] !== 'ended'): ?>
            setInterval(function() {
                window.location.reload();
            }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>