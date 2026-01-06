import re

class EntityExtractor:
    def extract_dates(self, text):
        # Extract dates like 12/12/2026 or "mañana"
        dates = re.findall(r'\d{1,2}/\d{1,2}/\d{2,4}', text)
        return dates

    def extract_money(self, text):
        # Extract usage of currency
        money = re.findall(r'Q\s?\d+(?:\.\d{2})?', text)
        return money
