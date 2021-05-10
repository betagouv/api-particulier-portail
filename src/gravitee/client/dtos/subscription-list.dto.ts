import { ApplicationDTOId } from 'src/gravitee/client/dtos/application-unit.dto';
import {
  PlanDTOId,
  SubscriptionDTOId,
} from 'src/gravitee/client/dtos/subscription-unit.dto';

export type SubscriptionListUnitDTO = {
  id: SubscriptionDTOId;
  application: {
    id: ApplicationDTOId;
    name: string;
  };
  plan: {
    id: PlanDTOId;
  };
};

export type SubscriptionListDTO = [SubscriptionListUnitDTO];
