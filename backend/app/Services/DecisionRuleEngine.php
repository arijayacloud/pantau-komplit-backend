<?php

namespace App\Services;

use App\Models\KonselingRule;

class DecisionRuleEngine
{
    public function run(string $kategori, array $data)
    {
        $rules = KonselingRule::where('kategori', $kategori)
            ->orderByDesc('priority')
            ->get()
            ->groupBy('rule_group');

        $hasil = [];
        $score = 0;
        $riskScore = 0;

        foreach ($rules as $group => $ruleGroup) {

            $logicGroups = $ruleGroup->groupBy('logic_group');
            $bestResult = null;

            foreach ($logicGroups as $logicKey => $ruleset) {

                $logicOperator = $ruleset->first()->logic_operator ?? 'AND';
                $evaluations = [];
                $explanations = [];

                foreach ($ruleset as $rule) {

                    if (!array_key_exists($rule->parameter, $data)) {
                        continue;
                    }

                    $current = $this->castValue($data[$rule->parameter], $rule->data_type);
                    $value = $this->castValue($rule->value, $rule->data_type);

                    $result = $this->evaluate($current, $rule->operator, $value);

                    $evaluations[] = $result;

                    $explanations[] = [
                        'parameter' => $rule->parameter,
                        'current' => $current,
                        'operator' => $rule->operator,
                        'target' => $value,
                        'result' => $result
                    ];
                }

                if (empty($evaluations)) continue;

                $passed = $logicOperator === 'AND'
                    ? !in_array(false, $evaluations, true)
                    : in_array(true, $evaluations, true);

                if ($passed) {
                    $rule = $ruleset->first();

                    $bestResult = [
                        'group' => $group,
                        'isi' => $rule->isi_konseling,
                        'score' => $rule->score,
                        'is_risk' => $rule->is_risk,
                        'priority' => $rule->priority,
                        'label' => $rule->label,
                        'explanation' => $explanations
                    ];

                    break; // ambil logic pertama yang match (priority tinggi)
                }
            }

            if ($bestResult) {
                $hasil[] = $bestResult;
                $score += $bestResult['score'] ?? 0;

                if ($bestResult['is_risk']) {
                    $riskScore += $bestResult['score'] ?? 1;
                }
            }
        }

        $filtered = collect($hasil)
            ->sortByDesc('priority')
            ->unique('label')
            ->values();

        return [
            'score' => $score,
            'risk_score' => $riskScore,
            'is_risk' => $riskScore >= 5,
            'hasil' => $filtered
        ];
    }

    private function castValue($value, $type)
    {
        return match ($type) {
            'number' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'string' => (string) $value,
            default => $value
        };
    }

    private function evaluate($current, $operator, $value)
    {
        return match ($operator) {
            '>' => $current > $value,
            '<' => $current < $value,
            '>=' => $current >= $value,
            '<=' => $current <= $value,
            '=', '==' => $current == $value,
            '!=' => $current != $value,

            // 🔥 TAMBAHAN
            'in' => in_array($current, (array) $value),
            'not_in' => !in_array($current, (array) $value),

            default => false
        };
    }
}
