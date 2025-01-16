const express = require('express');
const axios = require('axios');
const app = express();
const PORT = process.env.PORT || 3000;

// Middleware para configurar CORS
app.use((req, res, next) => {
  res.header('Access-Control-Allow-Origin', '*'); // Permite requisições de qualquer origem
  res.header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
  res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
  next();
});

// Rota para buscar e retornar o JSON
app.get('/strive/users', async (req, res) => {
  try {
    const response = await axios.get('https://databackend.koyeb.app/strive/users.json');
    res.json(response.data); // Retorna o JSON original
  } catch (error) {
    console.error('Erro ao buscar o JSON:', error.message);
    res.status(500).json({ error: 'Erro ao buscar os dados' });
  }
});

// Inicia o servidor
app.listen(PORT, () => {
  console.log(`Servidor rodando na porta ${PORT}`);
});
