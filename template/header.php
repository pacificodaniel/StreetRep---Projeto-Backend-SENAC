<header>
    
    <div class="header_area1">
        
        <a href=""> <img src="img/logo_png_4.png" alt="logo" height="250px" width="250px" class="page_logo"></a>
         <a href="" id="logo_a">STREET REP</a>
         
    
     </div>
     
     <P>Verifique locais de risco através de nosso sistema de reputação</P>
        
        <nav class="menu_header">
            <a href="index.php">Home</a>
            <a href="page1.php">Mapa</a>
            <a href="sobre.php">Sobre</a>
            <a href="contato.php">Contato</a>
           
            
        </nav>
        <div class="login_buttons">
    <?php if (isset($_SESSION['usuario_id'])): ?>
        <span class="text-warning" style="margin-right: 10px;">
            👤 <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?>
        </span>
        <span class="text-warning" style="margin-right: 10px;">
           <p class="text-warning">Verificado:
           <?php echo ($_SESSION['verificado'] ?? false) ? 'Sim ✅' : 'Não ❌'; ?>
           </p>
        
         
        </span>
        <a href="logout.php" class="btn btn-warning" id="button_sair_page1">Sair</a>
    <?php else: ?>
        <a href="cadastro.php" class="btn btn-warning">Cadastrar</a>
        <a href="login.php" class="btn btn-warning">Login</a>
    <?php endif; ?>
</div>
   
</header>
