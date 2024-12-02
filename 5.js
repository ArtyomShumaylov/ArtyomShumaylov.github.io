document.addEventListener('DOMContentLoaded', function() { 
    document.getElementById('calculateBtn').addEventListener('click', function() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const productPrice = parseFloat(document.getElementById('product').value);
        
        if (!quantity || quantity < 1) {
            document.getElementById('result').textContent = 'Введите корректное количество товара.';
            return;
        }
    
        const totalCost = quantity * productPrice;
        document.getElementById('result').textContent = `Стоимость заказа: ${totalCost} руб.`;
    });
}

);
