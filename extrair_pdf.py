import re
import json
import sys
from PyPDF2 import PdfReader


def extrair_consultas(pdf_path):
    consultas = []

    with open(pdf_path, 'rb') as file:
        reader = PdfReader(file)
        texto = ""

        # Extrai texto de todas as páginas
        for page in reader.pages:
            texto += page.extract_text() + "\n"

        # Salva texto para debug
        with open('debug_pdf_text.txt', 'w', encoding='utf-8') as f:
            f.write(texto)

        # Novo padrão regex para capturar o nome completo corretamente
        padrao = re.compile(
            r'(\d{2}/\d{2}/\d{4})\s*(\d{2}:\d{2})\s*'  # Data e Hora
            r'([A-Z][A-Z\s]+?[A-Z])\s*'  # Nome do paciente + possível nome do responsável
            r'(?:PUERICULTURA|CLINICA MEDICA|PEDIATRIA|PRE NATAL|PRE NATAL PRIMEIRA CONSULTA|CONSULTA ENFERMAGEM|CONSULTA).*?'  # Garante que pegamos apenas o paciente
            r'Telefones do paciente:\s*([^\n]+)',  # Telefones
            re.DOTALL
        )

        # Encontra todas as consultas
        for match in padrao.finditer(texto):
            data = match.group(1)
            hora = match.group(2)
            nome_completo = match.group(3).strip()
            telefones = match.group(4)

            # Limpa espaços múltiplos
            nome_completo = ' '.join(nome_completo.split())

            # Pega o nome completo, garantindo que "de", "da", "dos" ou "das" não interrompam
            partes_nome = nome_completo.split()

            if len(partes_nome) >= 2:  # Se houver pelo menos dois nomes
                # Verifica se o segundo nome é "de", "da", "dos", "das" e, se for, inclui o terceiro nome
                if partes_nome[1].lower() in ['de', 'da', 'dos', 'das']:
                    nome_formatado = ' '.join(partes_nome[:3])  # Pega os três primeiros nomes
                else:
                    nome_formatado = ' '.join(partes_nome[:2])  # Pega apenas os dois primeiros nomes
            else:
                nome_formatado = nome_completo  # Caso não haja dois nomes, mantém o nome original

            # Extrai todos os números de telefone
            tels = re.findall(r'\(\d+\)\s*\d+[\d\s-]*', telefones)

            for tel in tels:
                tel_formatado = re.sub(r'[^\d]', '', tel)
                if len(tel_formatado) >= 10:  # Número válido
                    consultas.append({
                        "telefone": tel_formatado,
                        "mensagem": f"Olá, {nome_formatado}! Sua consulta está agendada para o dia {data} às {hora} na USF Guapiranga. Esta é uma mensagem automática. Por favor, responda *SIM* para confirmar sua presença ou *NÃO* caso não possa comparecer. Sua confirmação é essencial para garantir o melhor atendimento, e faltar sem aviso pode tirar a vaga de outra pessoa que precisa. Contamos com sua colaboração!"
                    })

    # Salva no arquivo JSON
    with open('mensagem.json', 'w', encoding='utf-8') as f:
        json.dump(consultas, f, ensure_ascii=False, indent=4)

    return consultas


if __name__ == "__main__":
    if len(sys.argv) > 1:
        pdf_path = sys.argv[1]
        consultas = extrair_consultas(pdf_path)
        print(f"Extraídas {len(consultas)} consultas com sucesso!")
        if consultas:
            print("Exemplo de consultas extraídas:")
            for i, consulta in enumerate(consultas[:3], 1):
                print(f"{i}. Telefone: {consulta['telefone']}")
                print(f"   Mensagem: {consulta['mensagem'][:60]}...")
        else:
            print("Nenhuma consulta encontrada - verifique debug_pdf_text.txt")
    else:
        print("Erro: Caminho do PDF não fornecido.")
