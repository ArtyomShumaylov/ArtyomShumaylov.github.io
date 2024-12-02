document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const productTypeRadios = document.getElementsByName('productType');
    const optionSection = document.getElementById('optionSection');
    const propertySection = document.getElementById('propertySection');
    const optionSelect = document.getElementById('option');
    const propertyCheckbox = document.getElementById('property');
    const calculateBtn = document.getElementById('calculateBtn');
    const resultDiv = document.getElementById('result');

    const basePrices = {
        type1: 80,
        type2: 85,
        type3: 120
    };

    productTypeRadios.forEach(radio => {
        radio.addEventListener('change', handleProductTypeChange);
        radio.addEventListener('change', calculateTotal);
    });

    
    quantityInput.addEventListener('change', calculateTotal);
    productTypeRadios.addEventListener('change', calculateTotal);
    optionSection.addEventListener('change', calculateTotal);
    propertySection.addEventListener('change', calculateTotal);
    optionSelect.addEventListener('change', calculateTotal);
    propertyCheckbox.addEventListener('change', calculateTotal);
    

    function handleProductTypeChange() {
        const selectedType = document.querySelector('input[name="productType"]:checked').value;
        
        optionSection.style.display = 'none';
        propertySection.style.display = 'none';

        if (selectedType === 'type2') {
            optionSection.style.display = 'block';
        } else if (selectedType === 'type3') {
            propertySection.style.display = 'block';
        }
    }

    function calculateTotal() {
        const quantity = parseInt(quantityInput.value);
        if (!quantity || quantity < 1) {
            resultDiv.textContent = 'Введите корректное количество.';
            return;
        }

        const selectedType = document.querySelector('input[name="productType"]:checked').value;
        let totalCost = basePrices[selectedType] * quantity;

        if (selectedType === 'type2') {
            totalCost += parseFloat(optionSelect.value) * quantity;
        } else if (selectedType === 'type3' && propertyCheckbox.checked) {
            totalCost += parseFloat(propertyCheckbox.value) * quantity;
        }

        resultDiv.textContent = `Стоимость заказа: ${totalCost} руб.`;
    }
});
