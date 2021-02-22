import { KeyDTO } from 'src/gravitee/client/dtos/key.dto';
import { SubscriptionListUnitDTO } from 'src/gravitee/client/dtos/subscription-list.dto';
import { Brand } from 'src/types';

export type SubscriptionDTOId = Brand<string, 'SubscriptionDTOId'>;

export type PlanDTOId = Brand<string, 'PlanDTOId'>;

export type SubscriptionUnitDTO = SubscriptionListUnitDTO & {
  keys: [KeyDTO];
};
